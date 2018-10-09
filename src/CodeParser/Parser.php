<?php
/**
 * Created by PhpStorm.
 * User: lehadnk
 * Date: 10/7/18
 * Time: 10:09 PM
 */

namespace TranslationMergeTool\CodeParser;


use TranslationMergeTool\Config\Config;

class Parser
{
    const REGEXPS = [
        '"' => '/_?_(?:link)?\(\s*"(([^"\\\\]*(\\\\.[^"\\\\]*)*))"\s*(,|\))/m',
        "'" => '/_?_(?:link)?\(\s*\'(([^\'\\\\]*(\\\\.[^\'\\\\]*)*))\'\s*(,|\))/m',
    ];

    /**
     * @var Config
     */
    private $config;

    public function __construct(Config $config)
    {

    }

    private function getFileList(string $path): \RegexIterator
    {
        $directory = new \RecursiveDirectoryIterator($path);
        $iterator = new \RecursiveIteratorIterator($directory);
        $fileList = new \RegexIterator($iterator, '/^.+\.(?:php|js|vue)$/i', \RegexIterator::GET_MATCH);

        return $fileList;
    }

    public function getStrings(string $path, array $excludeDirectories, string $workingDir): array
    {
        $fileList = $this->getFileList($path);
        $filteredList = $this->filterFileList($fileList, $excludeDirectories, $workingDir);

        $strings = [];
        foreach($filteredList as $fileInfo) {
            $strings = array_merge($strings, $this->parseFile($fileInfo));
        }

        return $strings;
    }

    public function parseFile(string $path)
    {
        $content = file_get_contents($path);

        $result = [];
        foreach(self::REGEXPS as $regexp) {
            preg_match_all($regexp, $content, $regexpResult);
            $result = array_merge($result, array_unique($regexpResult[1]));
        }

        return $result;
    }

    private function filterFileList(\RegexIterator $fileList, array $excludeDirectories, string $workingDir)
    {
        $filteredList = [];
        foreach ($fileList as $fileInfo) {
            $excluded = false;
            foreach ($excludeDirectories as $excludeDirectory) {
                if (substr($fileInfo[0], 0, strlen($excludeDirectory) + strlen($workingDir) + 1) === $workingDir.'/'.$excludeDirectory) $excluded = true;
            }
            if (!$excluded) {
                $filteredList[] = $fileInfo[0];
            }
        }

        return $filteredList;
    }
}