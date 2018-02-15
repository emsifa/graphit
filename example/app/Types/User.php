<?php

namespace Example\App\Types;

use Emsifa\Graphit\Type;

class User extends Type
{

    public function resolveFieldAvatar($value)
    {
        return (isset($value['vatar']) && $value['avatar']) ? $value['avatar'] : 'uploads/default-avatar.png';
    }

}
