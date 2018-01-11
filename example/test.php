<?php

// Run this from terminal/cmd by running: php test.php

require(__DIR__.'/app/bootstrap.php');

$result = $graphit->execute("
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
