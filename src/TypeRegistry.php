<?php

namespace Emsifa\Graphit;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type as BaseType;

class TypeRegistry
{

    protected $graphit;

    protected $types = [];

    public function __construct(Graphit $graphit)
    {
        $this->graphit = $graphit;
    }

    /**
     * @api
     * @return IDType
     */
    public function id()
    {
        return BaseType::id();
    }

    /**
     * @api
     * @return StringType
     */
    public function string()
    {
        return BaseType::string();
    }

    /**
     * @api
     * @return BooleanType
     */
    public function boolean()
    {
        return BaseType::boolean();
    }

    /**
     * @api
     * @return IntType
     */
    public function int()
    {
        return BaseType::int();
    }

    /**
     * @api
     * @return FloatType
     */
    public function float()
    {
        return BaseType::float();
    }

    /**
     * @api
     * @param ObjectType|InterfaceType|UnionType|ScalarType|InputObjectType|EnumType|ListOfType|NonNull $wrappedType
     * @return ListOfType
     */
    public function listOf($wrappedType)
    {
        return BaseType::listOf($wrappedType);
    }

    /**
     * @api
     * @param ObjectType|InterfaceType|UnionType|ScalarType|InputObjectType|EnumType|ListOfType $wrappedType
     * @return NonNull
     */
    public function nonNull($wrappedType)
    {
        return BaseType::nonNull($wrappedType);
    }

    public function getObjectOrEnumType($name)
    {
        $key = strtolower($name);
        if (!isset($this->types[$key])) {
            $ast = $this->graphit->getAst();

            if ($ast->hasType($name)) {
                $this->types[$key] = $this->makeObjectType($name, ['name' => $name]);
                $config = $ast->getObjectTypeConfig($name);
                $this->types[$key]->config = $config;
            } elseif($ast->hasEnum($name)) {
                $this->types[$key] = $this->makeEnumType($name, ['name' => $name]);
                $config = $ast->getEnumTypeConfig($name);
                $this->types[$key]->setConfig($config);
            }
        }

        return $this->types[$key];
    }

    protected function makeObjectType($name, array $config)
    {
        $class = $this->graphit->getTypeClass($name);
        if (!class_exists($class)) {
            $class = Type::class;
        }
        return new $class($this->graphit, $config);
    }

    protected function makeEnumType($name, array $config)
    {
        $class = $this->graphit->getTypeClass($name);
        if (!class_exists($class)) {
            $class = Enum::class;
        }
        return new $class($this->graphit, $config);
    }

    public function __call($method, $args)
    {
        return $this->getObjectOrEnumType($method);
    }

}