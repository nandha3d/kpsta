<?php

namespace App\Models;

/**
 * Base class for the models carried over from CodeIgniter 3.
 *
 * CI3's CI_Model implemented __get() to forward any unknown property to the
 * active controller, which is how models reached $this->db, $this->load,
 * $this->session and $this->input without ever declaring them. That behaviour
 * is reproduced here, so the model bodies port across untouched.
 *
 * This is deliberately not CodeIgniter\Model: the CI4 base class brings its own
 * $db, $table, find*() and validation conventions that would collide with the
 * hand-written query methods these models already have.
 */
abstract class Ci3Model
{
    public function __construct()
    {
    }

    /**
     * Forward to the active controller, as CI_Model did.
     */
    public function __get(string $key)
    {
        $controller = get_instance();

        if ($controller === null) {
            return null;
        }

        return $controller->{$key} ?? null;
    }

    public function __isset(string $key): bool
    {
        $controller = get_instance();

        return $controller !== null && isset($controller->{$key});
    }

    /**
     * Forward method calls too, which a few models rely on for helpers that
     * live on the controller.
     */
    public function __call(string $name, array $arguments)
    {
        $controller = get_instance();

        if ($controller !== null && method_exists($controller, $name)) {
            return $controller->{$name}(...$arguments);
        }

        throw new \BadMethodCallException(sprintf('Call to undefined method %s::%s()', static::class, $name));
    }
}
