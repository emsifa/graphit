<?php

namespace Emsifa\Graphit\Concerns;

use Closure;
use Emsifa\Graphit\RootMutation;
use Emsifa\Graphit\RootQuery;
use GraphQL\Schema as GraphqlSchema;

trait Schema
{

    public function getSchemaFile()
    {
        return $this->option('schema');
    }

    public function buildSchema()
    {
        $query = $this->makeQuery();
        $mutation = $this->makeMutation();
        $schema = new GraphqlSchema([
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

}