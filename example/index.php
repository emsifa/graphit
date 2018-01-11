<?php

// Run this using PHP built-in server:
// php -S localhost:9000
//
// Test it with curl:
// curl -H "Content-Type: application/json" -X POST -d '{"query":"{books{id,title}}"}' http://localhost:9000
// Or test it in your browser:
// http://localhost:9000?query={books{id,title}}

require(__DIR__.'/app/bootstrap.php');

$result = $graphit->executeFromHttp();

header('Content-type: application/json');
echo json_encode($result, JSON_PRETTY_PRINT);
