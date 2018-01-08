<?php

namespace Example\App\Queries;

use Emsifa\Graphit\Query;

class Books extends Query
{

    public function resolve($root, $args, $ctx, $info)
    {
        return [
            [
                'id' => 1,
                'title' => 'Foo',
                'type' => 'MAGAZINE',
                // 'author' => [
                //     'id' => 1,
                //     'name' => 'Foobar'
                // ]
            ],
            [
                'id' => 2,
                'title' => 'Bar',
                'type' => 'NOVEL',
                // 'author' => [
                //     'id' => 1,
                //     'name' => 'Foobar'
                // ]
            ],
        ];
    }

}
