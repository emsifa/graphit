<?php

namespace Emsifa\Graphit\CLI;

use Rakit\Console\Command as BaseCommand;

abstract class Command extends BaseCommand
{

    public function requireGraphit($name = 'default')
    {
        $instance = $this->getGraphit($name);

        if (!$instance) {
            if ($name == 'default') {
                $message = "This command requires graphit instance.";
            } else {
                $message = "This command requires graphit instance '{$name}'.";
            }
            
            throw new \Exception($message);
        }

        return $instance;
    }

}