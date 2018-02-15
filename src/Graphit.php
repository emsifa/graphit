<?php

namespace Emsifa\Graphit;

use Closure;
use Emsifa\Graphit\FileType;
use GraphQL\GraphQL;
use GraphQL\Language\Parser;
use GraphQL\Upload\UploadType;
use GraphQL\Utils\AST as ASTUtils;

class Graphit
{

    use Concerns\Container;
    use Concerns\Http;
    use Concerns\GraphiQL;
    use Concerns\MacroInstance;
    use Concerns\MacroStatic;
    use Concerns\NamespaceAndClassGetter;
    use Concerns\Schema;

    protected $type;

    protected $cacher;
    protected $typeRegistry;
    protected $ast;

    protected $container = [];
    protected $options = [];

    public function __construct($options = [])
    {
        $this->options = array_merge($this->defaultOptions(), $options);
        if ($this->options['cache']) {
            $this->initCacher($this->options['cache']);
        }
        $this->initTypeRegistry();
        $this->initAst();
        $this->setType('File', function () {
            return new FileType;
        });
    }

    protected function initAst()
    {
        $cache = $this->getAstSchemaFromCache();
        $typeRegistry = $this->getTypeRegistry();
        if (!$cache) {
            $schemaFile = $this->option('schema');
            $document = Parser::parse(file_get_contents($schemaFile));
            $astArray = ASTUtils::toArray($document);
            $this->ast = AST::makeFromRawAst($astArray, $typeRegistry);
            if ($this->cacher) {
                $this->cacher->writeCache();
            }
        } else {
            $this->ast = new AST($cache, $typeRegistry);
        }
    }

    public function getAst()
    {
        return $this->ast;
    }

    public function getAstSchemaFromCache()
    {
        if (!$this->cacher) {
            return null;
        } else {
            return $this->cacher->getCache();
        }
    }

    public function setType($name, $type)
    {
        return $this->typeRegistry->setType($name, $type);
    }

    public function getType($name)
    {
        return $this->typeRegistry->getType($name);
    }

    protected function initCacher($cacheFile)
    {
        $this->cacher = new Cacher($this, $cacheFile);
    }

    protected function initTypeRegistry()
    {
        $this->typeRegistry = new TypeRegistry($this);
    }

    public function getTypeRegistry()
    {
        return $this->typeRegistry;
    }

    public function option($key)
    {
        return isset($this->options[$key]) ? $this->options[$key] : null;
    }

    public function getOptions()
    {
        return $this->options;
    }

    public function setOptions($options = [])
    {
        $this->options = array_merge($this->options, $options);
    }

    public function execute($gql, $variables = null, $operationName = null)
    {
        $schema = $this->buildSchema();

        try {
            $context = null;
            $result = GraphQL::executeQuery($schema, $gql, $this->option('root'), $context, $variables, $operationName);

            if (is_callable($this->option('error.formatter'))) {
                $result->setErrorFormatter($this->option('error.formatter'));
            }
            
            if (is_callable($this->option('error.handler'))) {
                $result->setErrorsHandler($this->option('error.handler'));
            }

            $debug = $this->option('debug');

            return $result->toArray();
        } catch (\Exception $e) {
            return [
                'errors' => [
                    [
                        'message' => $e->getMessage(),
                        'query' => $query
                    ]
                ]
            ];
        }
    }

    protected function defaultOptions()
    {
        return [
            'schema' => 'schema.graphql',
            'namespace' => 'App',
            'cache' => false,
            'rules' => [
                'query_depth' => 2,
                'query_complexity' => 2
            ],
            'root' => []
        ];
    }

}