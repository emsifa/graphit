<?php

require(__DIR__.'/../../vendor/autoload.php');
require(__DIR__.'/helpers.php');

use Emsifa\Graphit\Graphit;
use GraphQL\Error\Debug;

$graphit = new Graphit([
    'schema' => __DIR__.'/schema.graphql',
    'namespace' => 'Example\App',
    'cache' => __DIR__.'/cache.graphql.php',
    'debug' => Debug::INCLUDE_DEBUG_MESSAGE
]);

$graphit->usersRepository = function () {
    static $repository;
    if (!$repository) {
        $repository = new Example\App\Repositories\DummyUsersRepository;
    }
    return $repository;
};
