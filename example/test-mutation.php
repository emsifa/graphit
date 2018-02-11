<?php

// Run this from terminal/cmd by running: php test.php

require(__DIR__.'/app/bootstrap.php');

$result = $graphit->execute('
    mutation {
        register(input: {
            name: "tester"
            email: "tester@mail.com"
            password: "secret"
            role: admin
            # avatar: "foobar" # uncomment this, and you should get error
        }) {
            id
            name
            email
            password
            created_at
            updated_at
            deleted_at
        }
    }
');

echo json_encode($result, JSON_PRETTY_PRINT);
