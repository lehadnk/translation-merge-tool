<?php
/**
 * Created by PhpStorm.
 * User: lehadnk
 * Date: 10/7/18
 * Time: 2:20 PM
 */

namespace TranslationMergeTool;


use splitbrain\phpcli\CLI;
use splitbrain\phpcli\Options;
use TranslationMergeTool\CodeParser\Parser;
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

        $parser = new Parser($this->config);
        foreach ($this->config->components as $component) {
            $this->info("Parsing component {$component->name}...");

            $strings = [];
            foreach ($component->includeDirectories as $directory) {
                $strings = array_merge(
                    $strings,
                    $parser->getStrings(
                        $this->workingDir.'/'.$directory,
                        $component->excludeDirectories,
                        $this->workingDir
                    )
                );
            }

            $strings = array_unique($strings);
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
                $count = $reader->addNewTranslations($strings, $translationFile->absolutePath);
                $this->info("Added {$count} new strings...");
            }
        }
die();
        $this->info("Pushing updated files to bitbucket...");
        foreach ($affectedTranslationFiles as $translationFile) {
            $this->bitbucketAPI->pushFile($translationFile->relativePath, $translationFile->absolutePath, $this->config->translationBranchName);
        }

        $this->info("Pulling the weblate components...");
        $this->weblateAPI->pullComponent();

        $this->info("Downloading new translation files from weblate...");
        foreach ($affectedTranslationFiles as $translationFile) {
            $fileContents = $this->weblateAPI->downloadTranslation($translationFile->weblateCode);

            /**
             * @todo Здесь проверить что пришло с сервера перед записью!
             */
            file_put_contents($translationFile->absolutePath, $fileContents);
        }
    }
}