<?php

use Emsifa\Graphit\CLI\App;
use Emsifa\Graphit\CLI\Commands\ServeCommand;

$graphit = require('{BOOTSTRAP_FILE}');

// Initialize CLI Application
$cli = new App;

// Set Graphit Instance 
// To enable some commands that requires it
$cli->setGraphit($graphit);

// Register another commands
$cli->register(new ServeCommand([
    'public_path' => '{PUBLIC_PATH}',
    'file' => '{PUBLIC_FILE}',
    'host' => '127.0.0.1',
    'port' => 8080,
]));

$cli->run();
