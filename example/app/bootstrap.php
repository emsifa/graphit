<?php

require(__DIR__.'/../../vendor/autoload.php');
require(__DIR__.'/helpers.php');

use Emsifa\Graphit\Graphit;

$graphit = new Graphit([
    'schema' => __DIR__.'/schema.graphql',
    'namespace' => 'Example\App',
    'cache' => __DIR__.'/cache.graphql.php'
]);

$graphit->randomType = function () {
    $types = ['MAGAZINE', 'NOVEL', 'COMIC'];
    return $types[array_rand($types)];
};
