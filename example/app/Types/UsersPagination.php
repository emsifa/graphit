<?php

namespace Example\App\Types;

use Emsifa\Graphit\Type;

class UsersPagination extends Type
{

    public function resolveFieldTotal($val)
    {
        return 20;
    }

}
