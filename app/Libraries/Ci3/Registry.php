<?php

namespace App\Libraries\Ci3;

/**
 * Request-scoped state that the CI3-style global functions need to reach.
 *
 * CodeIgniter 3 kept a single controller instance that get_instance() handed
 * out, and the form/validation helpers read their state off it. CI4 has no
 * such singleton, so the pieces the application actually relies on are held
 * here and populated by BaseController as the request boots.
 */
final class Registry
{
    private static ?object $controller = null;

    /** @var array<string, string> */
    private static array $validationErrors = [];

    /** @var array<string, mixed>|null */
    private static ?array $oldInput = null;

    /** @var array<string, mixed> */
    private static array $config = [];

    public static function setController(object $controller): void
    {
        self::$controller = $controller;
    }

    /**
     * Returned by reference so `$CI =& get_instance()` behaves as it did.
     */
    public static function &controller(): ?object
    {
        return self::$controller;
    }

    /**
     * @param array<string, string> $errors
     */
    public static function setValidationErrors(array $errors): void
    {
        self::$validationErrors = $errors;
    }

    /**
     * @return array<string, string>
     */
    public static function validationErrors(): array
    {
        return self::$validationErrors;
    }

    /**
     * Value a field was submitted with, for re-populating a rejected form.
     */
    public static function oldInput(string $field)
    {
        if (self::$oldInput === null) {
            self::$oldInput = $_POST !== [] ? $_POST : $_GET;
        }

        // Support the name[key] syntax CI3's set_value() accepted.
        if (str_contains($field, '[')) {
            preg_match_all('/([^\[\]]+)/', $field, $m);
            $ref = self::$oldInput;
            foreach ($m[1] as $key) {
                if (! is_array($ref) || ! array_key_exists($key, $ref)) {
                    return null;
                }
                $ref = $ref[$key];
            }

            return $ref;
        }

        return self::$oldInput[$field] ?? null;
    }

    /**
     * @param array<string, mixed> $config
     */
    public static function mergeConfig(array $config): void
    {
        self::$config = array_merge(self::$config, $config);
    }

    public static function configItem(string $item)
    {
        return self::$config[$item] ?? null;
    }

    /**
     * Test seam: drop everything held for the current request.
     */
    public static function reset(): void
    {
        self::$controller       = null;
        self::$validationErrors = [];
        self::$oldInput         = null;
        self::$config           = [];
    }
}
