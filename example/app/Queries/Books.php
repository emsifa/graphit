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
                'title' => 'John Doe',
                'type' => $this->randomType,
                // 'author' => [
                //     'id' => 1,
                //     'name' => 'Foobar'
                // ]
            ],
            [
                'id' => 2,
                'title' => 'Jane Doe',
                'type' => $this->randomType,
                // 'author' => [
                //     'id' => 1,
                //     'name' => 'Foobar'
                // ]
            ],
        ];
    }

}
