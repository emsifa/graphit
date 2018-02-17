<?php

namespace Emsifa\Graphit\CLI\Commands;

use Emsifa\Graphit\CLI\Command;

class ServeCommand extends Command
{

    protected $signature = 'serve {router?} {--p|port=} {--h|host=} {--t|path=} {--f|file=}';

    protected $description = 'Running application using PHP built-in server.';

    protected $configs;

    public function __construct(array $configs = [])
    {
        $this->configs = array_merge([
            'public_path' => 'public_html',
            'host' => '127.0.0.1',
            'port' => 8080,
            'file' => ''
        ], $configs);
    }
    
    public function handle($router = null)
    {
        $file = $this->option('file') ?: $this->configs['file'];
        $port = $this->option('port') ?: $this->configs['port'];
        $host = $this->option('host') ?: $this->configs['host'];
        $publicPath = $this->option('public_path') ?: $this->configs['public_path'];

        $command = "php -S {$host}:{$port} {$router}";
        if ($publicPath) {
            $command .= " -t {$publicPath}";
        }
        $command = preg_replace("/ +/", " ", $command);

        $this->writeln("> ".$command, 'dark_gray');
        $this->writeln("------------------------------------------", 'blue');
        $this->writeln("Your GraphQL API is running on:");
        $this->writeln("http://{$host}:{$port}/{$file}", 'green');
        $this->writeln("------------------------------------------", 'blue');

        shell_exec($command);
    }

}
