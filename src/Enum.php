<?php

namespace Emsifa\Graphit;

use GraphQL\Type\Definition\EnumType;

class Enum extends EnumType
{  

    protected $graphkit;

    public function __construct(Graphit $graphkit, array $config)
    {
        $this->graphkit = $graphkit;
        parent::__construct($config);
    }

    public function setConfig(array $config)
    {
        $this->config = $config;
    }

}