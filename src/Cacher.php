<?php

namespace Emsifa\Graphit;

class Cacher
{

    protected $graphit;

    protected $cacheFile;

    public function __construct(Graphit $graphit, $cacheFile)
    {
        $this->graphit = $graphit;
        $this->cacheFile = $cacheFile;
    }

    public function getCache()
    {
        if (!file_exists($this->cacheFile)) {
            return null;
        } else {
            $schemaModifiedTime = filemtime($this->graphit->getSchemaFile());
            $cacheModifiedTime = filemtime($this->cacheFile);
            if ($schemaModifiedTime > $cacheModifiedTime) {
                return null;
            } else {
                return require($this->cacheFile);
            }
        }
    }

    public function writeCache()
    {
        $cacheContent = $this->generateCacheContent();
        return file_put_contents($this->cacheFile, $cacheContent);
    }

    protected function generateCacheContent()
    {
        $ast = $this->graphit->getAst()->getAst();

        ob_start();
        var_export($ast);
        $array = ob_get_clean();

        return implode("\n", [
            "<?php",
            "/*",
            " * ===================================================================",
            " * Graphit Cache File",
            " * ===================================================================",
            " * This file contains array of resolved graphql AST.",
            " * This file is auto generated whenever your '.graphql' file modified.",
            " * -------------------------------------------------------------------",
            " * YOU SHOULD NOT MODIFY THIS FILE MANUALLY.",
            " * -------------------------------------------------------------------",
            " */",
            "",
            "return ".$array.";"
        ]);
    }

}