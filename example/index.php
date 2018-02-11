<?php

use GraphQL\Error\Debug;

// Run this using PHP built-in server:
// php -S localhost:9000
//
// Test it with curl:
// curl -H "Content-Type: application/json" -X POST -d '{"query":"{books{id,title}}"}' http://localhost:9000
// Or test it in your browser:
// http://localhost:9000

require(__DIR__.'/app/bootstrap.php');

$requestHeaders = getallheaders();
$contentType = $requestHeaders['Content-Type'];

if ($contentType == 'application/json') {
    // Execute query
    $result = $graphit->executeFromHttp(null);
    
    header('Content-type: application/json');
    echo json_encode($result, JSON_PRETTY_PRINT);
} else {
    // Render graphiql
    echo $graphit->renderGraphiql();
}
