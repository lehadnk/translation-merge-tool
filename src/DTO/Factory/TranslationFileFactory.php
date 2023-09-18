<?php

namespace TranslationMergeTool\DTO\Factory;

use TranslationMergeTool\Config\Component;
use TranslationMergeTool\DTO\Locale;
use TranslationMergeTool\DTO\TranslationFile;

class TranslationFileFactory
{
    private string $workingDir;

    public function __construct(
        string $workingDir
    ) {
        $this->workingDir = $workingDir;
    }

    public function build(Component $component, Locale $locale): TranslationFile
    {
        $translationFile = new TranslationFile();
        $translationFile->relativePath = $component->getTranslationFileName($locale->localeName);
        $translationFile->absolutePath = rtrim($this->workingDir, '/').'/'.ltrim($translationFile->relativePath, '/');
        $translationFile->weblateCode = $locale->weblateCode;
        $translationFile->component = $component;
        $translationFile->isNew = !file_exists($translationFile->absolutePath);

        return $translationFile;
    }
}
