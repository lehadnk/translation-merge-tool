<?php

namespace TranslationMergeTool\Config;
use TranslationMergeTool\DTO\Locale;
use TranslationMergeTool\Exceptions\ConfigValidation\DirectoryNotFoundException;

class Component
{
    public string $name;
    public string $translationFileName;
    public string $weblateComponentSlug;
    public string $weblateProjectSlug;
    public bool $parseJavaAnnotations = false;
    public bool $compileMo = false;

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
