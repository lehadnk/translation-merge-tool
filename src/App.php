<?php
/**
 * Created by PhpStorm.
 * User: lehadnk
 * Date: 10/7/18
 * Time: 2:20 PM
 */

namespace TranslationMergeTool;


use Gettext\Translation;
use Gettext\Translations;

use splitbrain\phpcli\CLI;
use splitbrain\phpcli\Options;
use TranslationMergeTool\CodeParser\Parser;
use TranslationMergeTool\ComposerJson\ComposerJsonFactory;
use TranslationMergeTool\Config\Config;
use TranslationMergeTool\Config\ConfigFactory;
use TranslationMergeTool\DTO\TranslationFile;

class App extends CLI
{
    /**
     * @var string
     */
    private $workingDir;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var BitbucketAPI
     */
    private $bitbucketAPI;

    /**
     * @var WeblateAPI
     */
    private $weblateAPI;

    protected function setup(Options $options)
    {
        $options->setHelp('A tool for merging translation files for giftd projects');
        $options->registerOption('version', 'print version', 'v');
    }

    protected function main(Options $options)
    {
        if ($this->options->getOpt('version')) {
            $this->info($this->getVersion());
            exit(0);
        }

        $configFileName = $this->workingDir.'.translate-config.json';
        if (!file_exists($configFileName)) {
            $this->critical("Can't find translation config at ".$configFileName."! The application will terminate");
            exit(1);
        }
        $this->config = ConfigFactory::read($configFileName);

        $this->bitbucketAPI = new BitbucketAPI($this->config);
        $this->weblateAPI = new WeblateAPI($this->config);
        $this->workingDir = getcwd();

        $this->updateTranslations();
    }

    private function updateTranslations()
    {
        $this->info("Parsing code base...");

        /**
         * @var $affectedTranslationFiles TranslationFile[]
         */
        $affectedTranslationFiles = [];

        $currentBranchName = trim(`git rev-parse --abbrev-ref HEAD`);

        //$parser = new Parser($this->config);
        foreach ($this->config->components as $component) {
            $this->info("Parsing component {$component->name}...");

            $parser = new Parser($component, $this->workingDir, $currentBranchName);
            $strings = $parser->getStrings();

            $this->info(count($strings)." unique strings found!");

            $this->info("Parsing translation files...");
            foreach($this->config->locales as $locale) {
                $this->info("Parsing locale - {$locale->localeName}...");

                $translationFile = new TranslationFile();
                $translationFile->relativePath = $component->getTranslationFileName($locale->localeName);
                $translationFile->absolutePath = $this->workingDir.'/'.$translationFile->relativePath;
                $translationFile->weblateCode = $locale->weblateCode;
                $affectedTranslationFiles[] = $translationFile;

                $reader = new GettextReader($translationFile->absolutePath);
                $addedStrings = $reader->addNewTranslations($strings, $translationFile->absolutePath);

                $addedStringsCount = count($addedStrings);
                $addedStringsStr = implode("\n\t", $addedStrings);
                $this->info("Added $addedStringsCount new strings: \n $addedStringsStr\n\n");
            }
        }

        $this->info("Pushing updated files to bitbucket...");
        foreach ($affectedTranslationFiles as $translationFile) {
            $this->bitbucketAPI->pushFile($translationFile->relativePath, $translationFile->absolutePath, $this->config->translationBranchName);
        }

        $this->info("Pulling the weblate components...");
        $this->weblateAPI->pullComponent();


        $totalUpdated = 0;
        $total = count($affectedTranslationFiles);
        $this->info("Downloading new translation files from weblate...");
        foreach ($affectedTranslationFiles as $translationFile) {
            $oldFileHash = md5(file_get_contents($translationFile->absolutePath));

            $fileContents = $this->weblateAPI->downloadTranslation($translationFile->weblateCode);

            $newFileHash = md5($fileContents);

            if ($oldFileHash == $newFileHash) {
                $this->info("No changes for {$translationFile->absolutePath}...");
            } else {
                $this->info("Updating {$translationFile->absolutePath}...");
                $totalUpdated++;
            }


            $fileContents = $this->postProcessPoFile($fileContents);

            /**
             * @todo Здесь проверить что пришло с сервера перед записью!
             */
            file_put_contents($translationFile->absolutePath, $fileContents);

            $moPath = $translationFile->getAbsolutePathToMo();

            exec("msgfmt -o $moPath {$translationFile->absolutePath}");
        }

        $this->info("Total updated tranlsation files: $totalUpdated / $total");
    }

    private function getVersion()
    {
        $composerJson = ComposerJsonFactory::read();
        return $composerJson->version;
    }

    private function postProcessPoFile($poFileContents)
    {
        $translations = Translations::fromPoString($poFileContents);

        $translations = $this->removeMalformedDisabledTranslactions($translations);

        return $translations->toPoString();
    }

    /**
     * This method fixes weird msgfmt behaviour:
     *
     * An example of this weird behaviour:
     * common/i18n/ru_RU/LC_MESSAGES/default.po:20684: inconsistent use of #~
     * msgfmt: too many errors, aborting
     *
     * @param Translations $translations
     * @return Translations
     */
    private function removeMalformedDisabledTranslactions(Translations $translations)
    {
        $removedIndexes = [];
        $newTranslations = clone $translations;

        foreach ($translations as $i => $translation) {
            /**
             * @var Translation $translation
             */
            if ($translation->isDisabled()) {
                $isBeginningWithWhitespace =
                    preg_match("/^\s+/uis", $translation->getOriginal()) ||
                    preg_match("/^\s+/uis", $translation->getTranslation());


                if ($isBeginningWithWhitespace) {
                    $newTranslations->offsetUnset($i);
                    $removedIndexes[] = $i;
                }
            }
        }
        return $newTranslations;
    }



}