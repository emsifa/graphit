<?php

namespace Emsifa\Graphit;

use GraphQL\Type\Definition\ObjectType;

class Type extends ObjectType
{  

    protected $graphkit;

    public function __construct(Graphit $graphkit, array $config)
    {
        $this->graphkit = $graphkit;
        parent::__construct(array_merge($config, [
            'resolveField' => [$this, 'resolveField']
        ]));
    }

    public function resolveField($val, $args, $context, $info)
    {
        $method = 'resolveField'.$info->fieldName;
        if (is_callable([$this, $method])) {
            return $this->{$method}($val, $args, $context, $info);
        }
        return $val[$info->fieldName];
    }

    public function setConfig(array $config)
    {
        $this->config = $config;
    }

}