<?php

namespace Emsifa\Graphit\Concerns;

trait NamespaceAndClassGetter
{

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

}