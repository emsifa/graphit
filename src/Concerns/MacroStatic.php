<?php

namespace Emsifa\Graphit\Concerns;

use Closure;
use BadMethodCallException;

trait MacroStatic
{
    
    /**
     * The registered string macros.
     *
     * @var array
     */
    protected static $staticMacros = [];

    /**
     * Register a custom macro.
     *
     * @param  string    $name
     * @param  callable  $macro
     * @return void
     */
    public static function staticMacro($name, callable $macro)
    {
        static::$staticMacros[$name] = $macro;
    }

    /**
     * Checks if macro is registered.
     *
     * @param  string  $name
     * @return bool
     */
    public static function hasStaticMacro($name)
    {
        return isset(static::$staticMacros[$name]);
    }

    /**
     * Dynamically handle calls to the class.
     *
     * @param  string  $method
     * @param  array   $parameters
     * @return mixed
     *
     * @throws \BadMethodCallException
     */
    public static function __callStatic($method, $parameters)
    {
        if (! static::hasStaticMacro($method)) {
            throw new BadMethodCallException("Method {$method} does not exist.");
        }
        if (static::$staticMacros[$method] instanceof Closure) {
            return call_user_func_array(Closure::bind(static::$staticMacros[$method], null, static::class), $parameters);
        }
        return call_user_func_array(static::$staticMacros[$method], $parameters);
    }

}
