<?php

namespace Emsifa\Graphit\CLI\Commands;

use Emsifa\Graphit\CLI\Command;
use Emsifa\Graphit\CLI\Util;

class InitCommand extends Command
{

    protected $signature = 'init {dir}';

    protected $description = 'Init graphit skeleton app.';

    protected $skeletonDir;

    protected $publicFile;

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

        // Generate CLI App
        $generateCLI = $this->confirm("Do you want to generate CLI application in current directory?", false);
        if ($generateCLI) {
            $this->generateCLIApplication($appPath, $params);
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
        $params['URL_GRAPHIQL'] = basename($publicFile);
        $params['APP_PATH'] = Util::getRelativePath(dirname($publicFile), $appPath);
        $content = Util::replaceFileContents($sourceFile, $params);
        Util::putFile($publicFile, $content);
        $this->writeln("> Create file '{$publicFile}'.");

        $this->publicFile = $publicFile;
    }

    private function generateCLIApplication($appPath, array $params)
    {
        $workingDir = $this->getWorkingPath();
        $filename = $this->ask("What filename for this CLI application?", "graphit");
        $dest = $workingDir.'/'.$filename;
        
        if (is_file($dest)) {
            if (!$this->confirm("File '{$filename}' already exists. Do you want to replace it?", false)) {
                return;
            }
        }

        if ($this->publicFile) {
            $template = $this->skeletonDir.'/console-with-serve.php';
        } else {
            $template = $this->skeletonDir.'/console-without-serve.php';
        }

        $params['BOOTSTRAP_FILE'] = Util::getRelativePath($workingDir, realpath($appPath).'/graphit.php');
        if ($this->publicFile) {
            $params['PUBLIC_PATH'] = Util::getRelativePath($workingDir, dirname($this->publicFile));
            $params['PUBLIC_FILE'] = pathinfo($this->publicFile, PATHINFO_BASENAME);
        }
        $content = Util::replaceFileContents($template, $params);
        Util::putFile($dest, $content);
        $this->writeln("> Create file '{$filename}'");

        $this->write("Now you can run your CLI application by typing '", 'blue');
        $this->write("php {$filename} <command>", 'green');
        $this->writeln("' in this directory.", 'blue');
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
