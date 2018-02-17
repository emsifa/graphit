<?php

namespace Emsifa\Graphit\CLI;

class Util
{

    public static function getFiles ($dir, $ext = null, array $ignores = []) 
    {
        $results = [];
        $files = array_diff(scandir($dir), ['.', '..']);
        $ignoresRegex = $ignores ? '(' . implode('|', $ignores) . ')' : null;
        
        foreach ($files as $file) {
            $filepath = $dir . '/' . $file;

            if ($ignoresRegex && (bool) preg_match($ignoresRegex, $filepath)) {
                continue;
            }

            if (is_dir($filepath)) {
                $results = array_merge($results, static::getFiles($filepath, $ext));
            } else {
                $results[] = $filepath;
            }
        }

        return $results;
    }

    public static function replaceFileContents ($filepath, array $data)
    {
        return str_replace(
            array_map(function ($key) { return "{{$key}}"; }, array_keys($data)), 
            array_values($data), 
            file_get_contents($filepath)
        );
    }

    public static function putFile ($dest, $content)
    {
        $dir = dirname($dest);
        if (!is_dir($dir)) {
            static::makeDirectory($dir);
        }
        return file_put_contents($dest, $content);
    }

    public static function makeDirectory ($dir)
    {
        $dir = trim($dir);
        $paths = explode('/', $dir);
        $path = array_shift($paths);

        do {
            if ($path && !file_exists($path)) {
                mkdir($path);
            }
        } while ($paths && $path .= '/' . array_shift($paths));
    }

    public static function getRelativePath ($from, $to) 
    {
        $from = static::resolvePath($from);
        $to = static::resolvePath($to);
        $baseDir = '';

        if (0 === strpos($to, $from)) {
            return trim(substr($to, strlen($from)), '/');
        }

        $paths = explode("/", $from);
        while (array_pop($paths)) {
            $path = implode("/", $paths);
            if (0 === strpos($to, $path)) {
                $baseDir = $path;
                break;
            }
        }

        // Remove $baseDir from $from and $to
        $from = rtrim(substr($from, strlen($baseDir)), '/');
        $to = ltrim(substr($to, strlen($baseDir)), '/');

        // foo/bar -> ../..
        $pathMoveBack = preg_replace("/\w+/", "..", $from);
        return trim($pathMoveBack . '/' . $to, '/');
    }

    public static function resolvePath ($path)
    {
        // remove ./ or '/./' from $path
        $path = preg_replace("/(^\.\/|\/\.\/)/", "", $path);
        
        // If path is not absolute, make path absolute
        if (!preg_match("/^\//", $path)) {
            $workingDir = defined('WORKING_DIR') ? WORKING_DIR : realpath('');
            $path = $workingDir . '/' . $path;
        }

        // Remove dirname before '..'
        $resolvedPaths = [];
        $paths = explode("/", $path);
        while ($paths) {
            $p = array_shift($paths);

            if ($p == '..') {
                array_pop($resolvedPaths);
            } else {
                $resolvedPaths[] = $p;
            }
        }
        return implode("/", $resolvedPaths);
    }

}
