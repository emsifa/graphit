<?php

namespace Emsifa\Graphit;

class Query
{

    use Concerns\GraphitUtils;

    protected $graphit;

    public function __construct(Graphit $graphit)
    {
        $this->graphit = $graphit;
    }
    
}