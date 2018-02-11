<?php

namespace Emsifa\Graphit\Concerns;

use Closure;

trait Container
{

    public function has($key)
    {
        return isset($this->container[$key]);
    }

    public function __get($key)
    {
        if (!$this->has($key)) {
            return null;
        }

        $value = $this->container[$key];
        if ($value instanceof Closure) {
            return $value();
        } else {
            return $value;
        }
    }

    public function __set($key, $value)
    {
        if ($value instanceof Closure) {
            $value = $value->bindTo($this);
        }

        $this->container[$key] = $value;
    }

}