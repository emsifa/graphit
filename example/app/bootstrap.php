<?php

require(__DIR__.'/../../vendor/autoload.php');
require(__DIR__.'/helpers.php');

use Emsifa\Graphit\Graphit;

$graphit = new Graphit([
    'schema' => __DIR__.'/schema.graphql',
    'namespace' => 'Example\App',
]);
