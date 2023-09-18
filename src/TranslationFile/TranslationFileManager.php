<?php

namespace TranslationMergeTool\TranslationFile;

use TranslationMergeTool\CodeParser\CodeParser;
use TranslationMergeTool\Config\Config;
use TranslationMergeTool\DTO\Arguments;
use TranslationMergeTool\DTO\Factory\TranslationFileFactory;
use TranslationMergeTool\DTO\TranslationFile;
use TranslationMergeTool\Output\IOutputInterface;
use TranslationMergeTool\PoReader\GettextReader;

class TranslationFileManager
{
    private string $workingDir;
    private Config $config;
    private IOutputInterface $outputInterface;
    private Arguments $arguments;
    private CodeParser $codeParser;
    private TranslationFileFactory $translationFileFactory;

    public function __construct(
        string $workingDir,
        Config $config,
        Arguments $arguments,
        IOutputInterface $outputInterface,
        CodeParser $codeParser,
        TranslationFileFactory $translationFileFactory
    ) {
        $this->workingDir = $workingDir;
        $this->config = $config;
        $this->arguments = $arguments;
        $this->outputInterface = $outputInterface;
        $this->codeParser = $codeParser;
        $this->translationFileFactory = $translationFileFactory;
    }

    public function getAllTranslationFiles()
    {
        /** @var $allTranslationFiles TranslationFile[] */
        $allTranslationFiles = [];

        foreach ($this->config->components as $component) {
            foreach($component->getLocaleList($this->workingDir) as $locale) {
                $allTranslationFiles[] = $this->translationFileFactory->build($component, $locale);
            }
        }

        return $allTranslationFiles;
    }

    public function listAllUntranslatedStringsFromTheCurrentBranch(string $branchName)
    {
        $translations = $this->getAllTranslationFiles();

        $this->outputInterface->info("Untranslated string counts from $branchName:");

        $uniqueUntranslatedStrings = [];

        foreach ($translations as $translation) {
            if (!file_exists($translation->absolutePath)) {
                // There's a chance of file not existing in case if new translation language was added
                continue;
            }

            $reader = GettextReader::readFile($translation->absolutePath);
            $untranslated = $reader->getUntranslatedStringsAddedInBranch($branchName);

            $uniqueUntranslatedStrings = array_merge($uniqueUntranslatedStrings, $untranslated);

            $message = $translation->component->name . ':' . mb_strtoupper(substr($translation->weblateCode, 0, 2)) . "\t" . count($untranslated);
            if ($untranslated > 0) {
                $this->outputInterface->warning($message);
            } else {
                $this->outputInterface->success($message);
            }

            if ($this->arguments->printUntranslated) {
                foreach ($untranslated as $string) {
                    $this->outputInterface->info($string);
                }
            }
        }

        if ($uniqueUntranslatedStrings) {
            if (!$this->arguments->printUntranslated) {
                $this->outputInterface->info("Run this command to print the list of untranslated strings:");
                $this->outputInterface->info("i18n_mrg --print-untranslated");
            }

            $this->outputInterface->success("Translation update success");
        }
    }

    public function disableTranslations(array $translationFiles): int
    {
        $totalDisabledStrings = [];
        foreach ($translationFiles as $translationFile) {
            $translationStrings = $this->codeParser->getComponentStrings($translationFile->component, "master");
            $strings = [];
            foreach ($translationStrings as $string) {
                $strings[] = $string->originalString;
            }

            $reader = GettextReader::readFile($translationFile->absolutePath);
            $disabledTranslations = [];
            foreach ($reader->translations as $translation) {
                $original = $translation->getOriginal();
                if (!in_array($original, $strings)) {
                    $disabledTranslations[] = $original;
                    $totalDisabledStrings[$original] = $original;
                    $translation->setDisabled(true);
                }
            }
            $reader->save();

            $this->outputInterface->info($translationFile->relativePath.': '.count($disabledTranslations).' were disabled.');
            foreach ($disabledTranslations as $ds) {
                $this->outputInterface->debug($ds);
            }
        }

        return count($totalDisabledStrings);
    }

    public function addNewTranslationFile(string $localeName)
    {

    }
}
