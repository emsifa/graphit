<?php

// Run this from terminal/cmd by running: php test.php

require(__DIR__.'/app/bootstrap.php');

$result = $graphit->execute("
    mutation {
        createUser(name:\"tester\") {
            id
            name
        }
    }
");

echo json_encode($result, JSON_PRETTY_PRINT);
