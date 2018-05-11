<?php

namespace Emsifa\Graphit\Concerns;

use RuntimeException;
use UnexpectedValueException;

trait Playground
{

    public function renderPlayground(array $options = [])
    {
        $options = array_merge([
            'url_graphql' => '/graphql'
        ], $options);

        extract($options);

        ob_start();
        require(__DIR__ . '/../../playground/playground.php');
        return ob_get_clean();
    }

}