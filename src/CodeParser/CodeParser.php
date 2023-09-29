<?php

namespace TranslationMergeTool\CodeParser;

use TranslationMergeTool\Config\Component;
use TranslationMergeTool\Config\Config;
use TranslationMergeTool\DTO\CodeParseResult;
use TranslationMergeTool\DTO\Factory\TranslationFileFactory;
use TranslationMergeTool\DTO\TranslationFile;
use TranslationMergeTool\Output\IOutputInterface;
use TranslationMergeTool\PoReader\GettextReader;

class CodeParser
{
    private IOutputInterface $outputInterface;
    private string $workingDir;
    private Config $config;
    private TranslationFileFactory $translationFileFactory;

    public function __construct(
        string $workingDir,
        Config $config,
        IOutputInterface $outputInterface,
        TranslationFileFactory $translationFileFactory
    ) {
        $this->workingDir = $workingDir;
        $this->config = $config;
        $this->outputInterface = $outputInterface;
        $this->translationFileFactory = $translationFileFactory;
    }

    public function parseSources(string $branchName): CodeParseResult
    {
        $this->outputInterface->info("Parsing code base...");

        /** @var $affectedTranslationFiles TranslationFile[] */
        $affectedTranslationFiles = [];

        $newStringsTotal = 0;
        foreach ($this->config->components as $component) {
            $strings = $this->getComponentStrings($component, $branchName);
            $newComponentStrings = [];

            $this->outputInterface->info("Parsing translation files...");
            foreach($component->getLocaleList($this->workingDir) as $locale) {
                $this->outputInterface->info("Parsing locale - $locale->localeName...");

                $translationFile = $this->translationFileFactory->build($component, $locale);

                if ($translationFile->isNew) {
                    $reader = GettextReader::newFile($translationFile->absolutePath);
                } else {
                    $reader = GettextReader::readFile($translationFile->absolutePath);
                }

                $addedStrings = $reader->addNewTranslations($strings);
                $newComponentStrings = array_unique(array_merge($newComponentStrings, $addedStrings));
                $reader->save();

                $addedStringsCount = count($addedStrings);
                $addedStringsStr = implode("\n\t", $addedStrings);
                $this->outputInterface->info("Added $addedStringsCount new strings!");
                if ($addedStringsCount > 0) {
                    $this->outputInterface->debug("\n\t$addedStringsStr\n\n");
                    $affectedTranslationFiles[] = $translationFile;
                }
            }

            $newStringsTotal += count($newComponentStrings);
        }

        return new CodeParseResult($newStringsTotal, $affectedTranslationFiles);
    }

    public function getComponentStrings(Component $component, string $branchName)
    {
        $this->outputInterface->info("Parsing component {$component->name}...");

        $parser = new ComponentParser($component, $this->workingDir, $branchName);
        $strings = $parser->getStrings();

        $this->outputInterface->info(count($strings)." unique strings found!");

        return $strings;
    }
}
