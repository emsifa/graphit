<?php

use Rakit\Console\App;

require __DIR__ . '/Util.php';

define('WORKING_DIR', realpath(''));

$autoloadPaths = [
    __DIR__ . '/../../../autoload.php',
    __DIR__ . '/../vendor/autoload.php',
];

foreach ($autoloadPaths as $autoloadFile) {
    if (file_exists($autoloadFile)) {
        require($autoloadFile);
        define('VENDOR_DIR', dirname($autoloadFile));
        break;
    }
}

if (!defined('VENDOR_DIR')) {
    throw new Exception("Cannot run graphit CLI application. Vendor directory not found in any possible paths.");
}

$app = new App();

$app->command('init {dir}', 'Initialize graphit skeleton app', function ($appPath) {
    $namespace = trim($this->ask("Application namespace?", 'App'), '\\');
    $skeletonAppDir = __DIR__ . '/skeleton/app';
    $skeletonPublicDir = __DIR__ . '/skeleton/public';
    $appFiles = Util::getFiles($skeletonAppDir);

    // Get relative path to vendor dir
    $vendorDir = Util::getRelativePath($appPath, VENDOR_DIR);

    $params = [
        'NAMESPACE' => $namespace,
        'VENDOR_PATH' => $vendorDir,
    ];
    
    foreach ($appFiles as $file) {
        $dest = str_replace($skeletonAppDir, $appPath, $file);
        $content = Util::replaceFileContents($file, $params);
        Util::putFile($dest, $content);
        $this->writeln("> Create file '{$dest}'.");
    }

    $generatePublic = $this->confirm("Do you want to generate public file to handle http request in PHP native way?", true);
    if ($generatePublic) {
        $sourceFile = $skeletonPublicDir . '/index.php';
        $publicFile = $this->ask("Where should we put public file?", './public_html/graphql.php');
        if (!preg_match("/\.php$/", $publicFile)) {
            $publicFile .= '/graphql.php';
        }
        $params['APP_PATH'] = Util::getRelativePath(dirname($publicFile), $appPath);
        $content = Util::replaceFileContents($sourceFile, $params);
        Util::putFile($publicFile, $content);
        $this->writeln("> Create file '{$publicFile}'.");
    }
});

return $app;
