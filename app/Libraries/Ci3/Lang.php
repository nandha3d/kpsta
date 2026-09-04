<?php

namespace App\Libraries\Ci3;

/**
 * CodeIgniter 3's $this->lang.
 *
 * Aauth resolves every user-facing string through lang->line(), and the file
 * that backs it uses CI3's `$lang['key'] = 'text'` format. Rather than convert
 * those to CI4 language files and re-point ~90 call sites, the CI3 files are
 * read as-is from app/Language/Ci3.
 */
class Lang
{
    /** @var array<string, string> */
    private array $lines = [];

    /** @var list<string> */
    private array $loaded = [];

    /**
     * @param string $file Language file, with or without the _lang suffix.
     */
    public function load($file, string $idiom = 'english', bool $return = false, bool $addSuffix = true, string $altPath = '')
    {
        foreach ((array) $file as $name) {
            $name = str_replace(['_lang.php', '.php'], '', $name);

            if (in_array($name, $this->loaded, true) && ! $return) {
                continue;
            }

            $path = APPPATH . 'Language/Ci3/' . $idiom . '/' . $name . '_lang.php';

            if (! is_file($path)) {
                continue;
            }

            $lang = [];
            include $path;

            if ($return) {
                return $lang;
            }

            $this->lines  = array_merge($this->lines, $lang);
            $this->loaded[] = $name;
        }

        return $this->lines;
    }

    /**
     * CI3 returned FALSE for an unknown key; callers concatenate the result, so
     * the key itself is a more useful fallback than an empty string.
     */
    public function line(string $line, bool $logError = true)
    {
        return $this->lines[$line] ?? $line;
    }

    /**
     * @return array<string, string>
     */
    public function all(): array
    {
        return $this->lines;
    }
}
