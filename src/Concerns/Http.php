<?php

namespace Emsifa\Graphit\Concerns;

use GraphQL\Server\Helper;
use GraphQL\Server\StandardServer;
use GraphQL\Upload\UploadMiddleware;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\ServerRequestFactory;
use RuntimeException;
use UnexpectedValueException;

trait Http
{

    public function executeFromHttp(ServerRequestInterface $request = null, array $serverConfig = [])
    {
        if (!$request) {
            $request = $this->makePsrRequest();
        }

        $serverConfig = $this->mergeServerConfig($serverConfig);
        $server = new StandardServer($serverConfig);
        return $server->executePsrRequest($request);
    }

    protected function makePsrRequest()
    {
        $request = ServerRequestFactory::fromGlobals();
        $request = $request->withParsedBody(json_decode($request->getBody()->getContents(), true));
        $uploadMiddleware = new UploadMiddleware();
        $request = $uploadMiddleware->processRequest($request);

        return $request;
    }

    protected function mergeServerConfig(array $config)
    {
        if (!isset($config['debug']) && $debug = $this->option('debug')) {
            $config['debug'] = $debug;
        }
        if (!isset($config['errorFormatter']) && $formatter = $this->option('error.formatter')) {
            $config['errorFormatter'] = $formatter;
        }
        if (!isset($config['errorsHandler']) && $handler = $this->option('error.handler')) {
            $config['errorsHandler'] = $handler;
        }
        $config['schema'] = $this->buildSchema();
        return $config;
    }

}