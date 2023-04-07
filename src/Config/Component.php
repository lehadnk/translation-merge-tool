<?php
/**
 * Created by PhpStorm.
 * User: lehadnk
 * Date: 10/7/18
 * Time: 2:10 PM
 */

namespace TranslationMergeTool\Config;
use TranslationMergeTool\DTO\Locale;
use TranslationMergeTool\Exceptions\ConfigValidation\DirectoryNotFoundException;

class Component
{
    public string $name;
    public string $translationFileName;

    /**
     * @var string[]
     */
    public array $includePaths = [];

    /**
     * @var string[]
     */
    public array $excludePaths = [];

    public function getTranslationFileName(string $languageCode): string
    {
        return str_replace('{localeName}', $languageCode, $this->translationFileName);
    }

    /**
     * @param string $workingDir
     * @return Locale[]
     */
    public function getLocaleList(string $workingDir): array
    {
        $path = $workingDir.'/'.explode('{localeName}', $this->translationFileName)[0];

        $locales = [];
        if (!is_dir($path)) {
            throw new DirectoryNotFoundException("No directory named $path is found. Make sure that it exists in the project.");
        }

        $dir = new \DirectoryIterator($path);
        foreach ($dir as $fileInfo) {
            if ($fileInfo->isDir() && !$fileInfo->isDot()) {
                $locales[] = new Locale($fileInfo->getFilename());
            }
        }

        return $locales;
    }
}
