<?php

namespace Emsifa\Graphit\Concerns;

use RuntimeException;
use UnexpectedValueException;

trait Http
{

    public function executeFromHttp()
    {
        $input = $this->getInputsFromHttp();

        if (empty($input['query'])) {
            return [
                'errors' => [
                    [
                        'message' => "Empty query.",
                        'category' => 'graphql',
                        'locations' => [
                            [
                                'line' => 0,
                                'column' => 0
                            ]
                        ]
                    ]
                ]
            ];
        }

        return $this->execute($input['query'], $input['variables'], $input['operationName']);
    }

    public function getInputsFromHttp()
    {
        if (!isset($_SERVER['REQUEST_METHOD'])) {
            throw new RuntimeException("Cannot get inputs from HTTP. Invalid \$_SERVER variable. Make sure you running this using PHP CGI.");
        }

        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $input['query'] = isset($_GET['query']) ? $_GET['query'] : '';
            $input['variables'] = isset($_GET['variables']) ? $_GET['variables'] : null;
            $input['operationName'] = isset($_GET['operationName']) ? $_GET['operationName'] : null;
        } else {
            $body = file_get_contents('php://input');
            $input = array_merge([
                'query' => '',
                'variables' => null,
                'operationName' => null
            ], json_decode($body, true) ?: []);
        }

        return $input;
    }

}