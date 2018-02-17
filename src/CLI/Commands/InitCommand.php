<?php

namespace Emsifa\Graphit\CLI\Commands;

use Emsifa\Graphit\CLI\Command;
use Emsifa\Graphit\CLI\Util;

class InitCommand extends Command
{

    protected $signature = 'init {dir}';

    protected $description = 'Init graphit skeleton app.';

    protected $skeletonDir;

    public function __construct()
    {
        $this->skeletonDir = __DIR__ . '/../../../skeleton';
    }
    
    public function handle($appPath)
    {
        $namespace = trim($this->ask("Application namespace?", 'App'), '\\');

        $vendorDir = $this->findVendorDir();
        if (!$vendorDir) {
            throw new \UnexpectedValueException("We cannot find vendor directory in your project.");
        }

        $params = [
            'NAMESPACE' => $namespace,
            'VENDOR_PATH' => Util::getRelativePath($appPath, $vendorDir),
        ];

        // Generate Application Files
        $this->generateAppFiles($appPath, $params);

        // Generate Public Files
        $generatePublic = $this->confirm("Do you want to generate public file to handle http request in PHP native way?", true);
        if ($generatePublic) {
            $this->generatePublicFiles($appPath, $params);
        }
    }

    private function generateAppFiles($appPath, array $params)
    {
        $skeletonAppDir = $this->skeletonDir.'/app';
        $appFiles = Util::getFiles($skeletonAppDir);
        foreach ($appFiles as $file) {
            $dest = str_replace($skeletonAppDir, $appPath, $file);
            $content = Util::replaceFileContents($file, $params);
            Util::putFile($dest, $content);
            $this->writeln("> Create file '{$dest}'.");
        }
    }

    private function generatePublicFiles($appPath, array $params)
    {
        $skeletonPublicDir = $this->skeletonDir.'/public';
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

    private function findVendorDir()
    {
        $vendorDirs = [
            __DIR__.'/../../../../..',
            __DIR__.'/../../../vendor',
        ];

        if (defined('VENDOR_DIR')) {
            array_unshift($vendorDirs, VENDOR_DIR);
        }

        foreach ($vendorDirs as $dir) {
            if (is_dir($dir.'/composer') && is_file($dir.'/autoload.php')) {
                return realpath($dir);
            }
        }

        return $this->findVendorDirFromWorkingPath();
    
    }

    private function findVendorDirFromWorkingPath()
    {
        $workingPath = $this->getWorkingPath();
        if (!is_file($workingPath.'/composer.json')) {
            return null;
        }
        $composerJson = json_decode(file_get_contents($workingPath.'/composer.json'), true);
        $configs = isset($composerJson['config']) ? $composerJson['config'] : [];
        $vendorDir = isset($configs['vendor-dir']) ? $configs['vendor-dir'] : $workingPath.'/vendor';

        if (is_dir($vendorDir.'/composer') && is_file($vendorDir.'/autoload.php')) {
            return realpath($vendorDir);
        } else {
            return null;
        }
    }

    private function getWorkingPath()
    {
        return realpath('');
    }

}
