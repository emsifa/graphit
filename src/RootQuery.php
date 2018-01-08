<?php

namespace Emsifa\Graphit;

use GraphQL\Type\Definition\ObjectType;

class RootQuery extends ObjectType
{  

    protected $graphkit;

    public function __construct(Graphit $graphkit, array $queries)
    {
        $this->graphkit = $graphkit;
        parent::__construct([
            'name' => 'Query',
            'fields' => $queries,
            'resolveField' => [$this, 'resolveField']
        ]);
    }

    public function resolveField($val, $args, $context, $info)
    {
        $class = $this->graphkit->getQueryClass($info->fieldName);
        $queryResolver = new $class;
        return $queryResolver->resolve($val, $args, $context, $info);
    }

}