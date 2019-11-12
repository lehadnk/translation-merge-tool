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
use TranslationMergeTool\Config\ComposerJson\ComposerJsonFactory;
use TranslationMergeTool\Exceptions\ConfigValidation\ConfigValidationException;
use TranslationMergeTool\Exceptions\ConfigValidation\NoAuthCredentialsException;
use TranslationMergeTool\Exceptions\ConfigValidation\NoAuthTokenException;
use TranslationMergeTool\PoReader\PoPostProcessor;
use TranslationMergeTool\VcsAPI\VcsApiFactory;
use TranslationMergeTool\VcsAPI\IVcsApi;
use TranslationMergeTool\CodeParser\Parser;
use TranslationMergeTool\Config\Config;
use TranslationMergeTool\Config\ConfigFactory;
use TranslationMergeTool\DTO\TranslationFile;
use TranslationMergeTool\PoReader\GettextReader;
use TranslationMergeTool\WeblateAPI\MockWeblateAPI;
use TranslationMergeTool\WeblateAPI\WeblateAPI;

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
     * @var IVcsApi
     */
    private $vcsAPI;

    /**
     * @var WeblateAPI
     */
    private $weblateAPI;

    /**
     * @var int
     */
    private $newStringsTotal = 0;

    protected function setup(Options $options)
    {
        $options->setHelp('A tool for merging translation files for giftd projects');
        $options->registerOption('version', 'print version', 'v');
        $options->registerOption('just-parse', 'just parse code, no further actions', 'j');
        $options->registerOption('weblate-pull', 'just pull Weblate, no other actions', 'w');
        $options->registerOption('print-untranslated', 'print all untranslated strings from current branch');
        $options->registerOption('prune', 'mark all non-existing strings in project as disabled');
        $options->registerOption('force', 'pushes sources to repository and pulls component, even if no changes are found', 'f');
        $options->registerOption('no-weblate', 'skips all weblate-based operations');

        Console::setAppInstance($this);
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

        try {
            $this->config = ConfigFactory::read($configFileName);
        } catch (ConfigValidationException $e) {
            $this->error($e->getMessage());
            return;
        }

        try {
            $this->vcsAPI = VcsApiFactory::make($this->config);
        } catch (NoAuthCredentialsException $ex) {
            $this->error("Error! No {$this->config->vcs} authentication credentials found.");
            $this->info("Please consider adding I18N_MRG_VCS_USERNAME and I18N_MRG_VCS_PASSWORD environment variables into your ~\.bash_profile");
            exit(0);
        } catch (NoAuthTokenException $ex) {
            $this->error("Error! No {$this->config->vcs} authentication token found.");
            $this->info("Please consider adding I18N_MRG_VCS_AUTH_TOKEN environment variable into your ~\.bash_profile");
            exit(0);
        } catch (ConfigValidationException $ex) {
            $this->error($ex->getMessage());
            exit(0);
        }

        $this->weblateAPI = new WeblateAPI($this->config);
        if ($this->options->getOpt('no-weblate')) {
            $this->weblateAPI = new MockWeblateAPI();
        }
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

        if ($this->options->getOpt('prune')) {
            $this->prune();
            return;
        }

        if ($this->options->getArgs()) {
            $this->addLanguage($this->options->getOpt('add-lang'));
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
                $translationFile->component = $component;
                $translationFile->isNew = !file_exists($translationFile->absolutePath);
                $allTranslationFiles[] = $translationFile;

                if ($translationFile->isNew) {
                    $this->info("No translation file found for {$locale->localeName}. Creating a new one");
                    $this->addLanguage($locale->localeName, $translationFile);
                }
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
            $reader = GettextReader::readFile($translation->absolutePath);
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

    /**
     * @return TranslationFile[]
     * @throws Exceptions\ConfigValidation\DirectoryNotFoundException
     */
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

                $reader = GettextReader::readFile($translationFile->absolutePath);
                $addedStrings = $reader->addNewTranslations($strings);
                $reader->save();

                $addedStringsCount = count($addedStrings);
                $this->newStringsTotal += $addedStringsCount;
                $addedStringsStr = implode("\n\t", $addedStrings);
                $this->info("Added $addedStringsCount new strings!");
                $this->debug("$addedStringsStr\n\n");
            }
        }

        if ($this->newStringsTotal > 0) {
            $this->warning("i18n_mrg found $this->newStringsTotal new strings which are going to be pushed to weblate now. Should we continue? (Y/n)");

            $confirmation = readline();

            if (!($confirmation === 'Y' || $confirmation === 'y')) {
                $this->error("Aborting");
                exit(0);
            }
        }

        return $affectedTranslationFiles;
    }

    /**
     * @param TranslationFile[] $affectedTranslationFiles
     */
    private function pushToVcs(array $affectedTranslationFiles)
    {
        $this->info("Pushing updated files to {$this->config->vcs}...");

        if (count($affectedTranslationFiles) === 0) {
            $this->info("No translation files are updated, skipping pushing to VCS");
            return;
        }

        foreach ($affectedTranslationFiles as $translationFile) {
            $this->vcsAPI->addFile($translationFile);
        }

        $result = $this->vcsAPI->commit();

        if ($result->getStatusCode()[0] === 2) {
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

            $processor = new PoPostProcessor();
            $fileContents = $processor->postProcessPoFile($fileContents);

            /**
             * @todo Здесь проверить что пришло с сервера перед записью!
             */
            file_put_contents($translationFile->absolutePath, $fileContents);

            $moPath = $translationFile->getAbsolutePathToMo();

            exec("msgfmt -o $moPath {$translationFile->absolutePath}");
            if ($this->config->outputJson) {
                exec("i18next-conv -l {$translationFile->weblateCode} -s {$translationFile->absolutePath} -t {$translationFile->absolutePath}.json");
            }
        }

        $this->info("Total updated translation files: $totalUpdated / $total");
    }

    private function getVersion()
    {
        $composerJson = ComposerJsonFactory::read();
        return $composerJson->version;
    }

    private function getCurrentBranch()
    {
        return trim(`git branch | grep \* | cut -d ' ' -f2`);
    }

    private function prune()
    {
        if ($this->getCurrentBranch() !== 'master') {
            $this->error("The --prune argument could be run from master branch only! Please consider running: ".PHP_EOL."git checkout master");
            return;
        }

        $this->info("Are you sure that you merged all the currently active feature branches in master?");
        $confirmation = readline();

        if (!($confirmation === 'Y' || $confirmation === 'y')) {
            $this->error("You must merge all the current feature branches in master before running --prune command!");
            return;
        }

        $this->commitAndPushWeblateComponent();
        $translationFiles = $this->getAllTranslationFiles();
        $this->downloadTranslations($translationFiles);

        $totalDisabledStrings = [];
        foreach ($translationFiles as $translationFile) {
            $parser = new Parser($translationFile->component, $this->workingDir, 'master');
            $translationStrings = $parser->getStrings();

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

            $this->info($translationFile->relativePath.': '.count($disabledTranslations).' were disabled.');
            foreach ($disabledTranslations as $ds) {
                $this->debug($ds);
            }
        }

        $this->pushToVcs($translationFiles);
        $this->pullWeblateComponent();

        $this->success("Strings which are not used in the project are now marked as disabled. There are ".count($totalDisabledStrings)." of them in the project.");
    }

    private function addLanguage(string $language, TranslationFile $translationFile)
    {
        $currentBranchName = $this->getCurrentBranchName();

        foreach ($this->config->components as $component) {
            $this->info("Parsing component {$component->name}...");

            $parser = new Parser($component, $this->workingDir, $currentBranchName);
            $strings = $parser->getStrings();

            $fileName = $component->getTranslationFileName($language);

            $reader = GettextReader::newFile($fileName);
            $reader->addNewTranslations($strings);
            $reader->save();
        }

        $this->pushToVcs([$translationFile]);
        $this->commitAndPushWeblateComponent();
        $translationFile->isNew = false;
        $this->info("New language was added to the list. Now go to Weblate frontend, navigate to Manage > Repository maintenance, and click pull button, then re-run this tool after new language will be added.");
        exit(1);
    }
}