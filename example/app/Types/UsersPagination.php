<?php

namespace Example\App\Types;

use Emsifa\Graphit\Type;

class UsersPagination extends Type
{

    public function resolveFieldUsers($val)
    {
        return array_map(function ($user) {
            $user['name'] = $user['name'] . ' (hello from UsersPagination@resolveFieldUsers)';
            return $user;
        }, $val['users']);
    }

}
