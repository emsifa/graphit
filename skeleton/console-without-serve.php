<?php

use Emsifa\Graphit\CLI\App;

$graphit = require('{{ BOOTSTRAP_FILE }}');

// Initialize CLI Application
$cli = new App;

// Set Graphit Instance 
// To enable some commands that requires it
$cli->setGraphit($graphit);

// Register another commands
// $cli->register(new YourCustomCommand);

// Register command using closure
// $cli->command('your-command {arg}', 'description', function($arg) {
//      $this->writeln("Arg is: " . $arg);
// });

$cli->run();
