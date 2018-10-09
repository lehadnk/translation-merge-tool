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

    public function getStrings(string $path): array
    {
        $fileList = $this->getFileList($path);

        $strings = [];
        foreach($fileList as $fileInfo) {
            $path = $fileInfo[0];
            $strings = array_merge($strings, $this->parseFile($path));
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
}