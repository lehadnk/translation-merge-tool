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
use TranslationMergeTool\API\VcsApiFactory;
use TranslationMergeTool\API\VcsApiInterface;
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
     * @var VcsApiInterface
     */
    private $vcsAPI;

    /**
     * @var WeblateAPI
     */
    private $weblateAPI;

    protected function setup(Options $options)
    {
        $options->setHelp('A tool for merging translation files for giftd projects');
        $options->registerOption('version', 'print version', 'v');
        $options->registerOption('just-parse', 'just parse code, no further actions', 'j');
        $options->registerOption('weblate-pull', 'just pull Weblate, no other actions', 'w');
        $options->registerOption('print-untranslated', 'print all untranslated strings from current branch');
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

        $this->vcsAPI = VcsApiFactory::make($this->config);
        $this->weblateAPI = new WeblateAPI($this->config);
        $this->workingDir = getcwd();

        $this->updateTranslations();
    }

    private function updateTranslations()
    {
        if ($this->options->getOpt('weblate-pull')) {
            $this->pullWeblateComponent();
            return;
        }

        if ($this->options->getOpt('just-parse')) {
            $this->parseSources();
            return;
        }

        if ($this->options->getOpt('check')) {
            $this->listAllUntranslatedStringsFromTheCurrentBranch();
            return;
        }

        $this->commitAndPushWeblateComponent();

        // We need to get latest translations from Weblate before we parse the files, because we don't want
        // to lose translated strings by pushing empty translations
        $this->downloadTranslations($this->getAllTranslationFiles());

        $affectedTranslationFiles = $this->parseSources();
        $this->pushToVcs($affectedTranslationFiles);
        $this->pullWeblateComponent();
        $this->downloadTranslations($affectedTranslationFiles);
        $this->listAllUntranslatedStringsFromTheCurrentBranch();
    }

    private function getAllTranslationFiles()
    {
        /**
         * @var $allTranslationFiles TranslationFile[]
         */
        $allTranslationFiles = [];

        foreach ($this->config->components as $component) {
            foreach($component->getLocaleList($this->workingDir) as $locale) {
                $translationFile = new TranslationFile();
                $translationFile->relativePath = $component->getTranslationFileName($locale->localeName);
                $translationFile->absolutePath = $this->workingDir.'/'.$translationFile->relativePath;
                $translationFile->weblateCode = $locale->weblateCode;
                $allTranslationFiles[] = $translationFile;
            }
        }

        return $allTranslationFiles;
    }

    public function listAllUntranslatedStringsFromTheCurrentBranch()
    {
        $currentBranchName = $this->getCurrentBranchName();
        $translations = $this->getAllTranslationFiles();

        $this->info("Untranslated string counts from $currentBranchName:");

        $uniqueUntranslatedStrings = [];

        foreach ($translations as $translation) {
            $reader = new GettextReader($translation->absolutePath);
            $untranslated = $reader->getUntranslatedStringsAddedInBranch($currentBranchName);

            $uniqueUntranslatedStrings = array_merge($uniqueUntranslatedStrings, $untranslated);

            $message = mb_strtoupper(substr($translation->weblateCode, 0, 2)) . "\t" . count($untranslated);
            if ($untranslated > 0) {
                $this->warning($message);
            } else {
                $this->success($message);
            }

            if ($this->options->getOpt('print-untranslated')) {
                foreach ($untranslated as $string) {
                    $this->info($string);
                }
            }
        }

        if ($uniqueUntranslatedStrings) {
            if (!$this->options->getOpt('print-untranslated')) {
                $this->info("Run this command to print the list of untranslated strings:");
                $this->info("i18n_mrg --print-untranslated");
            }

            $this->warning("");
            $this->warning("Don't create PR until untranslated string count for RU is 0");
            $this->warning("");
        }
    }

    private function getCurrentBranchName()
    {
        return trim(`git rev-parse --abbrev-ref HEAD`);
    }

    public function parseSources()
    {
        $this->info("Parsing code base...");

        /**
         * @var $affectedTranslationFiles TranslationFile[]
         */
        $affectedTranslationFiles = [];

        $currentBranchName = $this->getCurrentBranchName();
        foreach ($this->config->components as $component) {
            $this->info("Parsing component {$component->name}...");

            $parser = new Parser($component, $this->workingDir, $currentBranchName);
            $strings = $parser->getStrings();

            $this->info(count($strings)." unique strings found!");

            $this->info("Parsing translation files...");
            foreach($component->getLocaleList($this->workingDir) as $locale) {
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
                $this->info("Added $addedStringsCount new strings!");
                $this->debug("$addedStringsStr\n\n");
            }
        }

        return $affectedTranslationFiles;
    }

    private function pushToVcs(array $affectedTranslationFiles)
    {
        $this->info("Pushing updated files to {$this->config->vcs}...");
        foreach ($affectedTranslationFiles as $translationFile) {
            $this->vcsAPI->addFile($translationFile->absolutePath, $translationFile->relativePath);
        }

        $result = $this->vcsAPI->commit();

        if ($result->getStatusCode() !== 201) {
            $this->error("Unable to push {$translationFile->relativePath} to the repository!");
            $this->debug($result->getStatusCode());
            $this->debug($result->getReasonPhrase());
            exit(2);
        }
    }

    private function commitAndPushWeblateComponent()
    {
        $this->info("Committing the current state of Weblate component...");
        $this->weblateAPI->commitComponent();

        $this->info("Pushing the current state of Weblate component...");
        $this->weblateAPI->pushComponent();
    }

    private function pullWeblateComponent()
    {
        $this->info("Pulling the Weblate component...");
        $this->weblateAPI->pullComponent();
    }

    private function downloadTranslations(array $affectedTranslationFiles)
    {
        $totalUpdated = 0;
        $total = count($affectedTranslationFiles);
        $this->info("Downloading new translation files from Weblate...");
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

        $this->info("Total updated translation files: $totalUpdated / $total");
    }

    private function getVersion()
    {
        $composerJson = ComposerJsonFactory::read();
        return $composerJson->version;
    }

    private function postProcessPoFile($poFileContents)
    {
        $translations = Translations::fromPoString($poFileContents);

        $translations = $this->removeMalformedTranslations($translations);

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
    private function removeMalformedTranslations(Translations $translations)
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