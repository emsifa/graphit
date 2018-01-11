<?php

namespace Emsifa\Graphit;

use GraphQL\Type\Definition\InputObjectType;

class Input extends InputObjectType
{

    protected $graphit;

    public function __construct(Graphit $graphit, array $config)
    {
        $this->graphit = $graphit;
        parent::__construct($this->mergeDefaults($config));
    }

    public function setConfig(array $config)
    {
        $this->config = $this->mergeDefaults($config);
    }

    public function getDefaults()
    {
        return [];
    }

    private function mergeDefaults(array $config)
    {
        $defaults = $this->getDefaults();
        foreach ($defaults as $key => $value) {
            if (!isset($config['fields'][$key])) continue;
            $config['fields'][$key]['defaultValue'] = $value;
        }
        return $config;
    }

}