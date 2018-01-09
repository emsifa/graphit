<?php

namespace Emsifa\Graphit;

use GraphQL\Type\Definition\ObjectType;

class RootQuery extends ObjectType
{  

    protected $graphit;

    public function __construct(Graphit $graphit, array $queries)
    {
        $this->graphit = $graphit;
        parent::__construct([
            'name' => 'Query',
            'fields' => $queries,
            'resolveField' => [$this, 'resolveField']
        ]);
    }

    public function resolveField($val, $args, $context, $info)
    {
        $class = $this->graphit->getQueryClass($info->fieldName);
        $queryResolver = new $class;
        return $queryResolver->resolve($val, $args, $context, $info);
    }

}