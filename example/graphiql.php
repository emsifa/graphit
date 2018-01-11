<?php

// Run this using PHP built-in server:
// php -S localhost:9000
//
// Open in in your browser
// http://localhost:9000/graphiql.php

require(__DIR__.'/app/bootstrap.php');

echo $graphit->renderGraphiql();
