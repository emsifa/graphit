<?php

namespace Example\App\Mutations;

use Emsifa\Graphit\Mutation;

class Register extends Mutation
{

    public function resolve($root, $args, $ctx, $info)
    {
        return $this->usersRepository->create($args['input']);
    }

}