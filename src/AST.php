<?php

namespace Emsifa\Graphit;

use Closure;

class AST
{

    protected $typeRegistry;

    protected $objectTypes = [];
    protected $enumTypes = [];
    protected $inputTypes = [];
    protected $interfaceTypes = [];

    protected $ast;

    public function __construct(array $ast, TypeRegistry $typeRegistry)
    {
        $this->typeRegistry = $typeRegistry;
        $this->ast = $ast;
    }

    public static function makeFromRawAst(array $ast, TypeRegistry $typeRegistry)
    {
        return new static(static::mapAstDefinitions($ast), $typeRegistry);
    }

    private static function mapAstDefinitions(array $ast)
    {
        $schema = null;
        $definitions = [
            'EnumTypeDefinition' => [],
            'ObjectTypeDefinition' => [],
            'SchemaDefinition' => [],
            'InterfaceTypeDefinition' => [],
            'InputObjectTypeDefinition' => []
        ];

        foreach ($ast['definitions'] as $definition) {
            if ($definition['kind'] === 'SchemaDefinition') {
                $schema = $definition;
            } else {
                $name = $definition['name']['value'];
                $definitions[$definition['kind']][$name] = $definition;
            }
        }

        $query = null;
        $mutation = null;

        if (isset($definitions['ObjectTypeDefinition']['Query'])) {
            $query = $definitions['ObjectTypeDefinition']['Query'];
            unset($definitions['ObjectTypeDefinition']['Query']);
        }

        if (isset($definitions['ObjectTypeDefinition']['Mutation'])) {
            $mutation = $definitions['ObjectTypeDefinition']['Mutation'];
            unset($definitions['ObjectTypeDefinition']['Mutation']);
        }

        return [
            'schema' => $schema,
            'query' => $query,
            'mutation' => $mutation,
            'types' => $definitions['ObjectTypeDefinition'],
            'enums' => $definitions['EnumTypeDefinition'],
            'interfaces' => $definitions['InterfaceTypeDefinition'],
            'inputs' => $definitions['InputObjectTypeDefinition'],
        ];
    }

    public function getAST()
    {
        return $this->ast;
    }

    public function hasEnum($name)
    {
        return isset($this->ast['enums'][$name]);
    }

    public function hasType($name)
    {
        return isset($this->ast['types'][$name]);
    }

    public function hasInterface($name)
    {
        return isset($this->ast['interfaces'][$name]);
    }

    public function hasInput($name)
    {
        return isset($this->ast['inputs'][$name]);
    }

    public function getQueries()
    {
        return $this->resolveAstFields($this->getQuery()['fields']);
    }

    public function getMutations()
    {
        return $this->resolveAstFields($this->getMutation()['fields']);
    }

    public function getQuery()
    {
        return $this->ast['query'];
    }

    public function getMutation()
    {
        return $this->ast['mutation'];
    }

    public function getType($name)
    {
        return $this->hasType($name) ? $this->ast['types'][$name] : null;
    }

    public function getEnum($name)
    {
        return $this->hasEnum($name) ? $this->ast['enums'][$name] : null;
    }

    public function getInterface($name)
    {
        return $this->hasInterface($name) ? $this->ast['interfaces'][$name] : null;
    }

    public function getInput($name)
    {
        return $this->hasInput($name) ? $this->ast['inputs'][$name] : null;
    }

    public function getObjectTypeConfig($name)
    {
        if (!isset($this->objectTypes[$name])) {
            $typeDef = $this->getType($name);

            if (!$typeDef) {
                throw new \UnexpectedValueException("Type '{$name}' is not defined in your .graphql file.");
            }

            $this->objectTypes[$name] = [
                'name' => $name,
                'description' => $this->resolveDescription($typeDef['description']),
                'fields' => $this->resolveAstFields($typeDef['fields']),
                'interfaces' => $this->resolveAstInterfaces($typeDef['interfaces'])
            ];
        }

        return $this->objectTypes[$name];
    }

    public function getEnumTypeConfig($name)
    {
        if (!isset($this->enumTypes[$name])) {
            $typeDef = $this->getEnum($name);

            if (!$typeDef) {
                throw new \UnexpectedValueException("Enum '{$name}' is not defined in your .graphql file.");
            }

            $this->enumTypes[$name] = [
                'name' => $name,
                'description' => $this->resolveDescription($typeDef['description']),
                'values' => $this->resolveAstValues($typeDef['values'])
            ];
        }

        return $this->enumTypes[$name];
    }

    public function getInterfaceTypeConfig($name)
    {
        if (!isset($this->interfaceTypes[$name])) {
            $typeDef = $this->getInterface($name);

            if (!$typeDef) {
                throw new \UnexpectedValueException("Interface '{$name}' is not defined in your .graphql file.");
            }

            $this->interfaceTypes[$name] = [
                'name' => $name,
                'description' => $this->resolveDescription($typeDef['description']),
                'fields' => $this->resolveAstFields($typeDef['fields'])
            ];
        }

        return $this->interfaceTypes[$name];
    }

    public function getInputTypeConfig($name)
    {
        if (!isset($this->inputTypes[$name])) {
            $typeDef = $this->getInput($name);

            if (!$typeDef) {
                throw new \UnexpectedValueException("Input type '{$name}' is not defined in your .graphql file.");
            }

            $this->inputTypes[$name] = [
                'name' => $name,
                'description' => $this->resolveDescription($typeDef['description']),
                'fields' => $this->resolveAstFields($typeDef['fields'])
            ];
        }

        return $this->inputTypes[$name];
    }

    protected function resolveAstFields(array &$astFields)
    {
        $fields = [];
        foreach ($astFields as $astField) {
            list($name, $config) = $this->resolveAstField($astField);
            $fields[$name] = $config;
        }
        return $fields;
    }

    protected function resolveAstValues(array &$astValues)
    {
        $values = [];
        foreach ($astValues as $astValue) {
            $val = $astValue['name']['value'];
            $values[$val] = [
                'value' => $val,
                'description' => $this->resolveDescription($astValue['description'])
            ];
        }
        return $values;
    }

    protected function resolveAstInterfaces(array &$astInterfaces)
    {
        return array_map(function($interface) {
            return $this->typeRegistry->getInterface($interface['name']['value']);
        }, $astInterfaces);
    }

    protected function resolveAstField(array &$astField)
    {
        $name = $astField['name']['value'];
        $args = !empty($astField['arguments']) ? $astField['arguments'] : [];
        $config = [
            'type' => $this->resolveAstType($astField['type']),
            'args' => $this->resolveAstArguments($args),
            'description' => $this->resolveDescription($astField['description'])
        ];

        return [$name, $config];
    }

    protected function resolveAstType(array &$astType)
    {
        if ($astType['kind'] === 'NonNullType') {
            return $this->typeRegistry->nonNull($this->resolveAstType($astType['type']));
        } elseif($astType['kind'] === 'ListType') {
            return $this->typeRegistry->listOf($this->resolveAstType($astType['type']));
        } else {
            $name = $astType['name']['value'];
            return $this->typeRegistry->{$name}();
        }
    }

    protected function resolveAstArguments(array &$astArguments)
    {
        $args = [];
        foreach ($astArguments as $astArg) {
            list($name, $config) = $this->resolveAstArgument($astArg);
            $args[$name] = $config;
        }
        return $args;
    }

    protected function resolveAstArgument(array &$astArg)
    {
        $name = $astArg['name']['value'];
        $config = [
            'type' => $this->resolveAstType($astArg['type']),
            'description' => $this->resolveDescription($astArg['description']),
            'defaultValue' => $astArg['defaultValue']['value']
        ];

        return [$name, $config];
    }

    protected function resolveDescription($desc)
    {
        return trim($desc);
    }

}