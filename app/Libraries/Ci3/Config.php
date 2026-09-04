<?php

namespace App\Libraries\Ci3;

/**
 * CodeIgniter 3's $this->config.
 *
 * Backed by the CI3-format config files kept under app/Config/Ci3. Those files
 * are plain `$config['key'] = value` arrays, which Aauth and the membership
 * controllers read wholesale via config->item('aauth').
 */
class Config
{
    /** @var array<string, mixed> */
    private array $config = [];

    /** @var list<string> */
    private array $loaded = [];

    public function load(string $file = '', bool $useSections = false, bool $failGracefully = false): bool
    {
        $file = str_replace('.php', '', $file);

        if (in_array($file, $this->loaded, true)) {
            return true;
        }

        $path = APPPATH . 'Config/Ci3/' . $file . '.php';

        if (! is_file($path)) {
            if ($failGracefully) {
                return false;
            }

            throw new \RuntimeException(sprintf('The configuration file %s.php does not exist.', $file));
        }

        $config = [];
        include $path;

        if ($useSections) {
            $this->config[$file] = array_merge($this->config[$file] ?? [], $config);
        } else {
            $this->config = array_merge($this->config, $config);
        }

        $this->loaded[] = $file;

        // Keep config_item() in step.
        Registry::mergeConfig($useSections ? [$file => $this->config[$file]] : $config);

        return true;
    }

    public function item(string $item, string $index = '')
    {
        if ($index === '') {
            return $this->config[$item] ?? null;
        }

        return $this->config[$index][$item] ?? null;
    }

    public function set_item(string $item, $value): void
    {
        $this->config[$item] = $value;
        Registry::mergeConfig([$item => $value]);
    }

    public function base_url(string $uri = ''): string
    {
        return base_url($uri);
    }

    public function site_url(string $uri = ''): string
    {
        return site_url($uri);
    }
}
