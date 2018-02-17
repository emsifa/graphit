<?php

use Emsifa\Graphit\Graphit;

require_once __DIR__ . '/{VENDOR_PATH}/autoload.php';

$graphit = new Graphit([
    'schema' => __DIR__ . '/schema.graphql',
    'namespace' => '{NAMESPACE}',
    'cache' => __DIR__ . '/cache.graphql.php',
]);

return $graphit;
