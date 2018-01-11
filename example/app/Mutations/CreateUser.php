<?php

namespace Example\App\Mutations;

use Emsifa\Graphit\Mutation;

class CreateUser extends Mutation
{

    public function resolve($root, $args, $ctx, $info)
    {
        return [
            'id' => 10,
            'name' => $args['input']['name'],
        ];
    }

}