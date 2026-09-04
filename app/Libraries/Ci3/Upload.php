<?php

namespace App\Libraries\Ci3;

use CodeIgniter\HTTP\Files\UploadedFile;

/**
 * CodeIgniter 3's upload library, over CI4's uploaded-file handling.
 *
 * The application uses do_upload()/data()/display_errors() and configures
 * allowed_types, upload_path, overwrite and remove_spaces. That is the surface
 * reproduced here, including data()'s array shape, which several controllers
 * index into by key (file_name, full_path, file_ext, ...).
 *
 * Note the extension check is driven by the client-supplied name only in CI3.
 * Here the real MIME type is consulted as well, so a .php renamed to .jpg is
 * rejected rather than stored.
 */
class Upload
{
    /** @var array<string, mixed> */
    private array $config = [];

    /** @var list<string> */
    private array $errors = [];

    /** @var array<string, mixed> */
    private array $data = [];

    private const DEFAULTS = [
        'upload_path'   => '',
        'allowed_types' => '',
        'max_size'      => 0,
        'max_width'     => 0,
        'max_height'    => 0,
        'overwrite'     => false,
        'encrypt_name'  => false,
        'remove_spaces' => true,
        'file_name'     => '',
    ];

    public function __construct(array $config = [])
    {
        $this->initialize($config);
    }

    public function initialize(array $config = [], bool $reset = true): static
    {
        $this->config = $reset
            ? array_merge(self::DEFAULTS, $config)
            : array_merge($this->config, $config);

        $this->errors = [];
        $this->data   = [];

        return $this;
    }

    public function do_upload(string $field = 'userfile'): bool
    {
        $this->errors = [];
        $this->data   = [];

        $request = service('request');
        $file    = $request->getFile($field);

        if (! $file instanceof UploadedFile) {
            $this->errors[] = 'You did not select a file to upload.';

            return false;
        }

        if (! $file->isValid()) {
            $this->errors[] = $file->getErrorString();

            return false;
        }

        $path = rtrim((string) $this->config['upload_path'], '/\\');
        if ($path === '') {
            $this->errors[] = 'The upload path does not appear to be valid.';

            return false;
        }

        if (! is_dir($path) && ! @mkdir($path, 0755, true) && ! is_dir($path)) {
            $this->errors[] = 'The upload path does not appear to be valid.';

            return false;
        }

        if (! is_writable($path)) {
            $this->errors[] = 'The upload destination folder does not appear to be writable.';

            return false;
        }

        $maxSize = (int) $this->config['max_size'];
        if ($maxSize > 0 && $file->getSize() > $maxSize * 1024) {
            $this->errors[] = 'The file you are attempting to upload is larger than the permitted size.';

            return false;
        }

        $originalName = $file->getClientName();
        $extension    = strtolower(ltrim(pathinfo($originalName, PATHINFO_EXTENSION), '.'));

        if (! $this->extensionAllowed($extension, $file)) {
            $this->errors[] = 'The filetype you are attempting to upload is not allowed.';

            return false;
        }

        $name = $this->config['file_name'] !== '' ? (string) $this->config['file_name'] : $originalName;
        $name = basename($name);

        if ($this->config['remove_spaces']) {
            $name = preg_replace('/\s+/', '_', $name);
        }

        if ($this->config['encrypt_name']) {
            $name = bin2hex(random_bytes(16)) . '.' . $extension;
        }

        // Never let a client-supplied name escape the upload directory.
        $name = str_replace(['/', '\\', "\0"], '', $name);
        if ($name === '' || $name[0] === '.') {
            $name = 'file_' . bin2hex(random_bytes(8)) . '.' . $extension;
        }

        if (! $this->config['overwrite']) {
            $name = $this->uniqueName($path, $name);
        }

        $sizeBytes = $file->getSize();
        $mimeType  = $file->getMimeType();

        try {
            $file->move($path, $name, true);
        } catch (\Throwable $e) {
            $this->errors[] = 'The file could not be written to disk.';

            return false;
        }

        $fullPath  = $path . DIRECTORY_SEPARATOR . $name;
        $imageSize = @getimagesize($fullPath);

        $this->data = [
            'file_name'         => $name,
            'file_type'         => $mimeType,
            'file_path'         => $path . DIRECTORY_SEPARATOR,
            'full_path'         => $fullPath,
            'raw_name'          => pathinfo($name, PATHINFO_FILENAME),
            'orig_name'         => $originalName,
            'client_name'       => $originalName,
            'file_ext'          => '.' . $extension,
            'file_size'         => round($sizeBytes / 1024, 2),
            'is_image'          => $imageSize !== false,
            'image_width'       => $imageSize[0] ?? null,
            'image_height'      => $imageSize[1] ?? null,
            'image_type'        => $imageSize !== false ? ltrim(image_type_to_extension($imageSize[2]), '.') : '',
            'image_size_str'    => $imageSize[3] ?? '',
        ];

        return true;
    }

    private function extensionAllowed(string $extension, UploadedFile $file): bool
    {
        $allowed = array_filter(array_map(
            'trim',
            explode('|', strtolower((string) $this->config['allowed_types']))
        ));

        if ($allowed === [] || $allowed === ['*']) {
            return true;
        }

        if (! in_array($extension, $allowed, true)) {
            return false;
        }

        // Guard against a disallowed file simply being renamed. Only reject on
        // a positive mismatch, so an unrecognised-but-allowed type still gets
        // through the way it did before.
        $real = $file->getMimeType();
        if ($real === null || $real === '' || $real === 'application/octet-stream') {
            return true;
        }

        $extFromMime = \Config\Mimes::guessExtensionFromType($real);

        if ($extFromMime === null) {
            return true;
        }

        $equivalent = [
            'jpg' => ['jpeg', 'jpg'], 'jpeg' => ['jpeg', 'jpg'],
            'doc' => ['doc'], 'docx' => ['docx', 'zip'],
            'xls' => ['xls'], 'xlsx' => ['xlsx', 'zip'], 'xlsm' => ['xlsm', 'zip'],
        ];
        $accepted = $equivalent[$extension] ?? [$extension];

        return in_array(strtolower($extFromMime), $accepted, true);
    }

    private function uniqueName(string $path, string $name): string
    {
        if (! file_exists($path . DIRECTORY_SEPARATOR . $name)) {
            return $name;
        }

        $base = pathinfo($name, PATHINFO_FILENAME);
        $ext  = pathinfo($name, PATHINFO_EXTENSION);
        $ext  = $ext === '' ? '' : '.' . $ext;

        for ($i = 1; $i < 1000; $i++) {
            $candidate = $base . $i . $ext;
            if (! file_exists($path . DIRECTORY_SEPARATOR . $candidate)) {
                return $candidate;
            }
        }

        return $base . '_' . bin2hex(random_bytes(4)) . $ext;
    }

    /**
     * @return array<string, mixed>|mixed
     */
    public function data(?string $index = null)
    {
        if ($index === null) {
            return $this->data;
        }

        return $this->data[$index] ?? null;
    }

    public function display_errors(string $open = '<p>', string $close = '</p>'): string
    {
        $out = '';
        foreach ($this->errors as $error) {
            $out .= $open . $error . $close;
        }

        return $out;
    }

    /**
     * @return list<string>
     */
    public function error_msg(): array
    {
        return $this->errors;
    }
}
