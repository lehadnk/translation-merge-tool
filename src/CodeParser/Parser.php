<?php
/**
 * Created by PhpStorm.
 * User: lehadnk
 * Date: 10/7/18
 * Time: 10:09 PM
 */

namespace TranslationMergeTool\CodeParser;


use TranslationMergeTool\Config\Component;
use TranslationMergeTool\Config\Config;
use TranslationMergeTool\DTO\TranslationString;

class Parser
{
    const REGEXPS = [
        '"' => '/_?_(?:link)?\(\s*"(([^"\\\\]*(\\\\.[^"\\\\]*)*))"\s*(,|\))/m',
        "'" => '/_?_(?:link)?\(\s*\'(([^\'\\\\]*(\\\\.[^\'\\\\]*)*))\'\s*(,|\))/m',
    ];


    /**
     * @var Component
     */
    private $component;

    /**
     * @var string
     */
    private $workingDir;

    /**
     * @var string
     */
    private $branchName;

    /**
     * @var TranslationString[]
     */
    private $translationStrings = [];

    public function __construct(Component $component, string $workingDir, string $branchName)
    {
        $this->component = $component;
        $this->workingDir = $workingDir;
        $this->branchName = $branchName;
    }

    private function getFileList(string $path): array
    {
        $directory = new \RecursiveDirectoryIterator($path);
        $iterator = new \RecursiveIteratorIterator($directory);
        $fileList = new \RegexIterator($iterator, '/^.+\.(?:php|js|vue)$/i', \RegexIterator::GET_MATCH);

        $list = [];
        foreach ($fileList as $file) {
            $list[] = $file[0];
        }

        return $list;
    }

    public function getStrings(): array
    {
        $this->translationStrings = [];
        foreach($this->component->includeDirectories as $directory) {
            $path = $this->workingDir.'/'.$directory;
            $fileList = $this->getFileList($path);
            if ($this->component->excludeDirectories) {
                $fileList = $this->filterFileList($fileList, $this->component->excludeDirectories, $this->workingDir);
            }

            foreach($fileList as $fileInfo) {
                $this->parseFile($fileInfo);
            }
        }

        return $this->translationStrings;
    }

    private function hasCyryllicCharacters($string)
    {
        return preg_match('/[А-Яа-яЁё]/u', $string);
    }

    public function parseFile(string $path)
    {
        $content = file_get_contents($path);

        $strings = [];
        foreach(self::REGEXPS as $regexp) {
            preg_match_all($regexp, $content, $regexpResult);
            $strings = array_unique(array_merge($strings, $regexpResult[1]));
        }

        $result = [];
        foreach ($strings as $string) {
            if ($this->hasCyryllicCharacters($string)) continue;

            $relativePath = substr($path, strlen($this->workingDir) + 1);
            if (array_key_exists($string, $this->translationStrings)) {
                $this->translationStrings[$string]->fileReferences[] = $relativePath;
            } else {
                $this->translationStrings[$string] = new TranslationString($string, [$relativePath], $this->branchName);
            }
        }

        return $result;
    }

    private function filterFileList(array $fileList, array $excludeDirectories, string $workingDir)
    {
        $filteredList = [];
        foreach ($fileList as $path) {
            $excluded = false;

            foreach ($excludeDirectories as $excludeDirectory) {
                $searchSubstr = $workingDir . '/' . $excludeDirectory;
                if (substr($path, 0, strlen($searchSubstr)) === $searchSubstr) $excluded = true;
            }

            if (!$excluded) {
                $filteredList[] = $path;
            }
        }

        return $filteredList;
    }
}