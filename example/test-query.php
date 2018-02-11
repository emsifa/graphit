<?php

// Run this from terminal/cmd by running: php test.php

require(__DIR__.'/app/bootstrap.php');

$result = $graphit->execute("
    {
        usersPagination (
            limit: 20
            offset: 5
        ) {
            users {
                id
                name
                email
                role
            }
            total
        }
    }
");

echo json_encode($result, JSON_PRETTY_PRINT);
