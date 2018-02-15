<?php

namespace Example\App\Types;

use Emsifa\Graphit\Type;

class User extends Type
{

    public function resolveFieldAvatar($value)
    {
        if (!isset($value['avatar']) || !$value['avatar']) {
            return 'uploads/default-avatar.png';
        } else {
            return $value['avatar'];
        }
    }

}
