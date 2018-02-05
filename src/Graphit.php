<?php

namespace Emsifa\Graphit;

use GraphQL\GraphQL;
use GraphQL\Language\Parser;
use GraphQL\Schema;
use GraphQL\Utils\AST as ASTUtils;
use GraphQL\Utils\BuildSchema;
use GraphQL\Validator\DocumentValidator;
use GraphQL\Validator\Rules\QueryComplexity;
use GraphQL\Validator\Rules\QueryDepth;
use Closure;

class Graphit
{

    use Concerns\Http;
    use Concerns\GraphiQL;
    use Concerns\MacroInstance;
    use Concerns\MacroStatic;

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

    public function getTypeFactory()
    {
        if (!$this->type) {
            $this->initTypeFactory();
        }
        return $this->type;
    }

    public function getSchemaFile()
    {
        return $this->option('schema');
    }

    public function getMutationNamespace()
    {
        return $this->option('namespace').'\Mutations';
    }

    public function getQueryNamespace()
    {
        return $this->option('namespace').'\Queries';
    }

    public function getInterfaceNamespace()
    {
        return $this->option('namespace').'\Interfaces';
    }

    public function getTypeNamespace()
    {
        return $this->option('namespace').'\Types';
    }

    public function getTypeClass($class)
    {
        return $this->getTypeNamespace().'\\'.ucfirst($class);
    }

    public function getInputClass($class)
    {
        return $this->getTypeNamespace().'\\'.ucfirst($class);
    }

    public function getInterfaceClass($class)
    {
        return $this->getInterfaceNamespace().'\\'.ucfirst($class);
    }

    public function getQueryClass($class)
    {
        return $this->getQueryNamespace().'\\'.ucfirst($class);
    }

    public function getMutationClass($class)
    {
        return $this->getMutationNamespace().'\\'.ucfirst($class);
    }

    public function execute($gql, $variables = null, $operationName = null)
    {
        $schema = $this->buildSchema();

        try {
            $result = GraphQL::executeQuery($schema, $gql, $this->option('root'), $variables, $operationName);

            if (is_callable($this->option('error.formatter'))) {
                $result->setErrorFormatter($this->option('error.formatter'));
            }
            
            if (is_callable($this->option('error.handler'))) {
                $result->setErrorsHandler($this->option('error.handler'));
            }

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

    public function buildSchema()
    {
        $query = $this->makeQuery();
        $mutation = $this->makeMutation();
        $schema = new Schema([
            'query' => $query,
            'mutation' => $mutation
        ]);

        return $schema;
    }

    protected function makeQuery()
    {
        $queries = $this->ast->getQueries();
        return new RootQuery($this, $queries);
    }

    protected function makeMutation()
    {
        $mutations = $this->ast->getMutations();
        return new RootMutation($this, $mutations);
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

    public function has($key)
    {
        return isset($this->container[$key]);
    }

    public function __get($key)
    {
        if (!$this->has($key)) {
            return null;
        }

        $value = $this->container[$key];
        if ($value instanceof Closure) {
            return $value();
        } else {
            return $value;
        }
    }

    public function __set($key, $value)
    {
        if ($value instanceof Closure) {
            $value = $value->bindTo($this);
        }

        $this->container[$key] = $value;
    }

}