<?php

function dump_ast(array $ast, $filename = 'dump.schema.json') {
    file_put_contents($filename, json_encode($ast, JSON_PRETTY_PRINT));
    echo file_get_contents($filename);
    exit;
}

function dd() {
    var_dump(func_get_args());
    exit;
}
