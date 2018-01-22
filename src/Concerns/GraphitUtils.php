<?php

namespace Emsifa\Graphit\Concerns;

trait GraphitUtils
{

    public function getGraphit()
    {
        return $this->graphit;
    }

    public function __get($key)
    {
        return $this->getGraphit()->{$key};
    }

    public function __set($key, $value)
    {
        return $this->getGraphit()->{$key} = $value;
    }

    public function __call($method, $args)
    {
        $graphit = $this->getGraphit();
        if (is_callable([$graphit, $method])) {
            return call_user_func_array([$graphit, $method], $args);
        } else {
            return parent::__call($method, $args);
        }
    }

}