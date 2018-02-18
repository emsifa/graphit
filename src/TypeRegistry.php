<?php

namespace Emsifa\Graphit;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type as BaseType;
use Closure;
use InvalidArgumentException;
use RuntimeException;

class TypeRegistry
{

    protected $graphit;

    protected $types = [];

    protected $interfaces = [];

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

    public function getInterface($name)
    {
        $key = strtolower($name);
        if (!isset($this->interfaces[$key])) {
            $this->interfaces[$key] = $this->makeInterfaceType($name, ['name' => $name]);
            $config = $this->graphit->getAst()->getInterfaceTypeConfig($name);
            $this->interfaces[$key]->setConfig($config);
        }
        return $this->interfaces[$key];
    }

    public function getType($name)
    {
        $key = $name;
        if (!isset($this->types[$key])) {
            $ast = $this->graphit->getAst();

            if ($ast->hasType($name)) {
                $this->types[$key] = $this->makeObjectType($name, ['name' => $name]);
                $config = $ast->getObjectTypeConfig($name);
                $this->types[$key]->config = $config;
                $this->types[$key]->description = $config['description'];
            } elseif($ast->hasEnum($name)) {
                $this->types[$key] = $this->makeEnumType($name, ['name' => $name]);
                $config = $ast->getEnumTypeConfig($name);
                $this->types[$key]->description = $config['description'];
                $this->types[$key]->setConfig($config);
            } elseif($ast->hasInput($name)) {
                $this->types[$key] = $this->makeInputType($name, ['name' => $name]);
                $config = $ast->getInputTypeConfig($name);
                $this->types[$key]->description = $config['description'];
                $this->types[$key]->setConfig($config);
            } else {
                throw new RuntimeException("Type '{$name}' is not defined.");
            }
        } elseif (is_string($this->types[$key])) {
            $this->types[$key] = new $this->types[$key];
        } elseif ($this->types[$key] instanceof Closure) {
            $this->types[$key] = $this->types[$key]->bindTo($this->graphit)();
            $this->types[$key]->name = $name;
        }

        return $this->types[$key];
    }

    public function setType($name, $type)
    {
        if (!is_string($type) && false == $type instanceof Closure && false == $type instanceof BaseType) {
            throw new InvalidArgumentException("Type definition must be string class name, closure, or instanceof '".BaseType::class."'.");
        }
        $this->types[$name] = $type;
    }

    protected function makeInterfaceType($name, array $config)
    {
        $class = $this->graphit->getInterfaceClass($name);
        if (!class_exists($class)) {
            $class = InterfaceType::class;
        }
        return new $class($this->graphit, $config);
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

    protected function makeInputType($name, array $config)
    {
        $class = $this->graphit->getInputClass($name);
        if (!class_exists($class)) {
            $class = Input::class;
        }
        return new $class($this->graphit, $config);
    }

    public function __call($method, $args)
    {
        return $this->getType($method);
    }

}