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
    const EXTENSIONS = ['php', 'vue', 'js'];

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

    public function getStrings(string $path): array
    {
        $iterator = new \RecursiveDirectoryIterator($path, \RecursiveDirectoryIterator::SKIP_DOTS);
        $strings = [];
        foreach($iterator->getChildren() as $child) {
            /**
             * @var $child \SplFileInfo
             * @todo Убрать когда станет понятно почему не обходит все файлы
             */
//            var_dump($child->getFileInfo()->getPathname());

            if (in_array($child->getExtension(), self::EXTENSIONS)) {
                $path = $child->getFileInfo()->getPathname();
                $strings = array_merge($strings, $this->parseFile($path));
            }
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