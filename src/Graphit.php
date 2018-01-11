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

class Graphit
{

    use Concerns\Http;
    use Concerns\GraphiQL;

    protected $type;

    protected $options = [];

    public function __construct($options = [])
    {
        $this->options = array_merge($this->defaultOptions(), $options);
        $this->initTypeRegistry();
        $this->initAst();
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
        $astArray = $this->getAstArray();
        $this->ast = new AST($astArray, $this->getTypeRegistry());
    }

    public function getAst()
    {
        return $this->ast;
    }

    public function getAstArray()
    {
        $cache = $this->getCacheAst();
        if (!$cache) {
            $schemaFile = $this->option('schema');
            $document = Parser::parse(file_get_contents($schemaFile));
            $astArray = ASTUtils::toArray($document);
        }

        return $astArray;
    }

    public function getCacheAst()
    {
        
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

}