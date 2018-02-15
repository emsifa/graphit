<?php

namespace Emsifa\Graphit\Concerns;

use Emsifa\Graphit\Helper;
use GraphQL\Server\StandardServer;
use GraphQL\Upload\UploadMiddleware;
use Psr\Http\Message\ServerRequestInterface;
use RuntimeException;
use UnexpectedValueException;
use Zend\Diactoros\ServerRequestFactory;

trait Http
{

    public function executeFromHttp(ServerRequestInterface $request = null, array $serverConfig = [])
    {
        if (!$request) {
            $request = $this->makePsrRequest();
        }

        if ($this->isRequestMultipart($request)) {
            $request = $this->resolveRequestMultipart($request);
        }

        $serverConfig = $this->mergeServerConfig($serverConfig);
        $server = new StandardServer($serverConfig);
        return $server->executePsrRequest($request);
    }

    protected function makePsrRequest()
    {
        return ServerRequestFactory::fromGlobals();
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

    protected function isRequestMultipart(ServerRequestInterface $request)
    {
        $contentTypes = $request->getHeader('content-type');
        if (!$contentTypes) return false;
        return strpos($contentTypes[0], 'multipart/form-data') === 0;
    }

    protected function resolveRequestMultipart(ServerRequestInterface $request)
    {
        $this->validateRequestMultipart($request);
        $uploadedFiles = $request->getUploadedFiles();
        $parsedBody = $request->getParsedBody();
        $operations = json_decode($parsedBody['operations'], true);
        $map = isset($parsedBody['map']) ? json_decode($parsedBody['map'], true) : [];
        foreach ($map as $key => $position) {
            $file = Helper::arrayGet($uploadedFiles, $key);
            Helper::arraySet($operations, $position, $file);
        }

        return $request
            ->withHeader('content-type', 'application/json')
            ->withParsedBody($operations);
    }

    protected function validateRequestMultipart(ServerRequestInterface $request)
    {
        if (($method = $request->getMethod()) != 'POST') {
            throw new \UnexpectedValueException("Request multpart/form-data only support 'POST' method. Instead got: '{$method}'.");
        }

        $parsedBody = $request->getParsedBody();
        if (!isset($parsedBody['operations'])) {
            throw new \UnexpectedValueException("Request multipart/form-data require 'operations' data.");
        }

        $operations = json_decode($parsedBody['operations'], true);
        if (is_null($operations)) {
            throw new \UnexpectedValueException("Operations is not valid JSON.");
        }

        if (isset($operations['map'])) {
            $map = json_decode($operations['map'], true);
            if (is_null($operations['map'])) {
                throw new \UnexpectedValueException("Map is not valid JSON.");
            }
        }
    }

}