<?php

namespace Example\App\Types;

use Emsifa\Graphit\Type;

class Book extends Type
{

    public function resolveFieldAuthor($val, $args, $ctx, $info)
    {
        return [
            'id' => 1,
            'name' => 'asd'
        ];
    }

}
