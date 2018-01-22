<?php

namespace Emsifa\Graphit;

use GraphQL\Type\Definition\EnumType;

class Enum extends EnumType
{  

    use Concerns\GraphitUtils;

    protected $graphit;

    public function __construct(Graphit $graphit, array $config)
    {
        $this->graphit = $graphit;
        parent::__construct($config);
    }

    public function setConfig(array $config)
    {
        $this->config = $config;
    }

}