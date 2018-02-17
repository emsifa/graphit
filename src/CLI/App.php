<?php

namespace Emsifa\Graphit\CLI;

use Rakit\Console\App as ConsoleApp;
use Emsifa\Graphit\Graphit;

class App extends ConsoleApp
{

    protected $graphitInstances = [];

    public function __construct($argv = null)
    {
        if (!$argv) {
            global $argv;
        }
        parent::__construct($argv);
        $this->registerBasicCommands();
    }

    private function registerBasicCommands()
    {
        $this->register(new Commands\InitCommand);
    }

    public function setGraphit(Graphit $instance, $name = 'default')
    {
        $this->graphitInstances[$name] = $instance;
    }

    public function getGraphit($name = 'default')
    {
        return $this->hasGraphit($name) ? $this->graphitInstances[$name] : null;
    }

    public function hasGraphit($name = 'default')
    {
        return isset($this->graphitInstances[$name]);
    }

    public function getGraphitInstances($name = 'default')
    {
        return $this->graphitInstances;
    }

}