<?php

namespace Example\App\Mutations;

use Emsifa\Graphit\Mutation;

class Register extends Mutation
{

    public function resolve($root, $args, $ctx, $info)
    {
        $data = $args['input'];
        if (isset($data['avatar']) &&$avatar = $data['avatar']) {
            $avatar->moveTo(__DIR__.'/../../uploads/' . $avatar->getClientFilename());
            $data['avatar'] = 'uploads/' . $avatar->getClientFilename();
        }
        return $this->usersRepository->create($data);
    }

}