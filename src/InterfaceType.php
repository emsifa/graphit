<?php

namespace Emsifa\Graphit;

use GraphQL\Type\Definition\InterfaceType as BaseInterfaceType;
use GraphQL\Type\Definition\ResolveInfo;

class InterfaceType extends BaseInterfaceType
{  
    use Concerns\GraphitUtils;

    protected $graphit;

    public function __construct(Graphit $graphit, array $config)
    {
        $this->graphit = $graphit;
        parent::__construct(array_merge($config, [
            'resolveType' => [$this, 'resolveType']
        ]));
    }

    public function resolveType($val, $context, ResolveInfo $info)
    {
        $method = 'resolveType'.$val;
        if (is_callable([$this, $method])) {
            return $this->{$method}($val, $context, $info);
        }
        return $val[$info->fieldName];
    }

    public function setConfig(array $config)
    {
        $this->config = $config;
    }

}