<?php

namespace Emsifa\Graphit;

use GraphQL\Type\Definition\ObjectType;

class RootMutation extends ObjectType
{  
    use Concerns\GraphitUtils;

    protected $graphit;

    public function __construct(Graphit $graphit, array $mutations)
    {
        $this->graphit = $graphit;
        parent::__construct([
            'name' => 'Mutation',
            'fields' => $mutations,
            'resolveField' => [$this, 'resolveField']
        ]);
    }

    public function resolveField($val, $args, $context, $info)
    {
        $class = $this->graphit->getMutationClass($info->fieldName);
        $mutationResolver = new $class($this->graphit);
        return $mutationResolver->resolve($val, $args, $context, $info);
    }

}