<?php

namespace Example\App\Queries;

use Emsifa\Graphit\Query;

class UsersPagination extends Query
{

    public function resolve($root, $args, $ctx, $info)
    {
        $users = $this->usersRepository->all();
        return [
            'users' => $users,
            'total' => count($users)
        ];
    }

}
