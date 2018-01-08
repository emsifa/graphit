<?php

namespace Example\App\Types;

use Emsifa\Graphit\Type;

class User extends Type
{

    public function resolveFieldBooks()
    {
        return [
            [
                'id' => 1,
                'title' => 'Author Book',
            ],
            [
                'id' => 2,
                'title' => 'Author Book 2',
            ]
        ];
    }

}
