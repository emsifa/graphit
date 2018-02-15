<?php

// Run this from terminal/cmd by running: php test.php

require(__DIR__.'/app/bootstrap.php');

$result = $graphit->execute('
    mutation ($input: RegisterInput!) {
        register(input: $input) {
            id
            name
            email
            password
            role
            created_at
            updated_at
            deleted_at
        }
    }
', [
    'input' => [
        'name' => "tester",
        'email' => 'tester@mail.com',
        'password' => 'secret',
        'role' => 'admin'
    ]
]);

echo json_encode($result, JSON_PRETTY_PRINT);
