<?php

require(__DIR__.'/../vendor/autoload.php');
require(__DIR__.'/helpers.php');

use Emsifa\Graphit\Graphit;

$graphkit = new Graphit([
    'schema' => __DIR__.'/schema.graphql',
    'namespace' => 'Example\App',
    'error.handler' => function ($errors) {
        foreach ($errors as $err) {
            echo PHP_EOL;
            echo $err->message;
            echo PHP_EOL;
            echo $err->getFile().' line '.$err->getLine();
            echo PHP_EOL;
        }
        exit;
    }
]);

$result = $graphkit->execute("
    {
        books {
            id,
            title,
            type,
            author {
                id
                name
                books {
                   id,
                   title
                }
            }
        }
    }
");

echo json_encode($result, JSON_PRETTY_PRINT);
