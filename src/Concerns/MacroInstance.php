<?php

namespace Emsifa\Graphit\Concerns;

use Closure;
use BadMethodCallException;

trait MacroInstance
{
    
    /**
     * The registered string macros.
     *
     * @var array
     */
    protected $macros = [];

    /**
     * Register a custom macro.
     *
     * @param  string    $name
     * @param  callable  $macro
     * @return void
     */
    public function macro($name, callable $macro)
    {
        $this->macros[$name] = $macro;
    }

    /**
     * Checks if macro is registered.
     *
     * @param  string  $name
     * @return bool
     */
    public function hasMacro($name)
    {
        return isset($this->macros[$name]);
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
    public function __call($method, $parameters)
    {
        if (! static::hasMacro($method)) {
            throw new BadMethodCallException("Method {$method} does not exist.");
        }
        if ($this->macros[$method] instanceof Closure) {
            return call_user_func_array($this->macros[$method]->bindTo($this, static::class), $parameters);
        }
        return call_user_func_array($this->macros[$method], $parameters);
    }
}
