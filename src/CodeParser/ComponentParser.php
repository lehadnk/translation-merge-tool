<?php

namespace TranslationMergeTool\CodeParser;


use TranslationMergeTool\Config\Component;
use TranslationMergeTool\DTO\TranslationString;

class ComponentParser
{
    private const REGEXPS = [
        '"' => [
            '/[^a-zA-Z0-9]t(?:link)?\(\s*"(([^"\\\\]*(\\\\.[^"\\\\]*)*))"\s*(,|\))/m',
            '/[^a-zA-Z0-9]_?_(?:link)?\(\s*"(([^"\\\\]*(\\\\.[^"\\\\]*)*))"\s*(,|\))/m',
        ],
        "'" => [
            '/[^a-zA-Z0-9]t(?:link)?\(\s*\'(([^\'\\\\]*(\\\\.[^\'\\\\]*)*))\'\s*(,|\))/m',
            '/[^a-zA-Z0-9]_?_(?:link)?\(\s*\'(([^\'\\\\]*(\\\\.[^\'\\\\]*)*))\'\s*(,|\))/m',

        ],
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

    /**
     * @var FileLister
     */
    private $fileLister;

    public function __construct(Component $component, string $workingDir, string $branchName)
    {
        $this->component = $component;
        $this->workingDir = $workingDir;
        $this->branchName = $branchName;
        $this->fileLister = new FileLister();
    }

    /**
     * @return TranslationString[]
     */
    public function getStrings(): array
    {
        $this->translationStrings = [];

        foreach($this->fileLister->getFileList($this->component, $this->workingDir) as $file) {
            $this->parseFile($file);
        }

        return $this->translationStrings;
    }

    private function hasCyryllicCharacters($string): bool
    {
        return preg_match('/[А-Яа-яЁё]/u', $string);
    }

    /**
     * @param string $path
     * @return string[]
     */
    public function parseFile(string $path): array
    {
        $content = file_get_contents($path);

        $strings = [];

        foreach(self::REGEXPS as $quoteType => $regexps) {
            foreach ($regexps as $regexp) {
                preg_match_all($regexp, $content, $regexpResult);
                $regexpResult = $this->escapeStrings($quoteType, $regexpResult);
                $strings = array_unique(array_merge($strings, $regexpResult[1]));
            }
        }

        if ($this->component->parseJavaAnnotations) {
            $annotationParseRegexp = '/((@[a-zA-Z0-9]*([\ \r\n]+)?\(([\ \r\n]+)?[\w\W]*?message([\ \r\n]+)?=([\ \r\n]*?"{)))\K[\s\S]*?(?=}")/';
            preg_match_all($annotationParseRegexp, $content, $regexpResult);

            $regexpResult = $this->escapeStrings('"', $regexpResult[0]);
            $strings = array_unique(array_merge($strings, $regexpResult));
        }

        $result = [];
        foreach ($strings as $string) {
            if ($this->hasCyryllicCharacters($string)) continue;

            $relativePath = substr($path, strlen($this->workingDir) + 1);
            $result[] = $string;
            if (array_key_exists($string, $this->translationStrings)) {
                $this->translationStrings[$string]->fileReferences[] = $relativePath;
            } else {
                $this->translationStrings[$string] = new TranslationString($string, [$relativePath], $this->branchName);
            }
        }

        return $result;
    }

    private function escapeStrings(string $quoteType, array &$strings): array
    {
        foreach ($strings as &$result) {
            // We are removing all escaped quotes from the string
            // Example #1: __("this is a \"quote\" ") => this is a "quote"
            // Example #2: __('this is a \'quote\' ') => this is a 'quote'
            $result = str_replace("\\$quoteType", $quoteType, $result);
        }

        return $strings;
    }
}
