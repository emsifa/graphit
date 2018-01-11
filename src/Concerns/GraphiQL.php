<?php

namespace Emsifa\Graphit\Concerns;

use RuntimeException;
use UnexpectedValueException;

trait GraphiQL
{

    public function renderGraphiql(array $options = [])
    {
        $options = array_merge([
            'url_graphql' => '/graphql'
        ], $options);

        extract($options);

        ob_start();
        require(__DIR__.'/../../graphiql/graphiql.php');
        return ob_get_clean();
    }

}