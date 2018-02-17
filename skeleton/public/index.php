<?php

$graphit = require(__DIR__ . '/{APP_PATH}/graphit.php');

$requestHeaders = getallheaders();
$contentType = $requestHeaders['Content-Type'];

if ($contentType == 'application/json') {
    // Execute query
    $result = $graphit->executeFromHttp();
    header('Content-type: application/json');
    echo json_encode($result);
} else {
    // Render graphiql
    echo $graphit->renderGraphiql([
        'url_graphql' => '/{URL_GRAPHIQL}'
    ]);
}
