<?php

namespace TranslationMergeTool\Application;
use TranslationMergeTool\CodeParser\CodeParser;
use TranslationMergeTool\Config\ComposerJson\ComposerJsonFactory;
use TranslationMergeTool\Config\Config;
use TranslationMergeTool\DTO\Arguments;
use TranslationMergeTool\DTO\CodeParseResult;
use TranslationMergeTool\Environment\Environment;
use TranslationMergeTool\Output\IOutputInterface;
use TranslationMergeTool\System\Git;
use TranslationMergeTool\System\Msgfmt;
use TranslationMergeTool\TranslationFile\TranslationFileManager;
use TranslationMergeTool\VcsAPI\IVcsApi;
use TranslationMergeTool\VcsAPI\RepositoryManager;
use TranslationMergeTool\WeblateAPI\IWeblateAPI;
use TranslationMergeTool\WeblateAPI\WeblateManager;

class Application
{
    private string $workingDir;
    private Arguments $arguments;
    private IOutputInterface $outputInterface;
    private Git $git;
    private Msgfmt $msgfmt;
    private Environment $environment;
    private IVcsApi $vcsApi;
    private IWeblateAPI $weblateApi;
    private Config $config;
    private TranslationFileManager $translationFileManager;
    private RepositoryManager $repositoryManager;
    private CodeParser $codeParser;

    public function __construct(
        string $workingDir,
        Arguments $arguments,
        Environment $environment,
        IOutputInterface $outputInterface,
        Git $git,
    ) {
        $this->workingDir = $workingDir;
        $this->arguments = $arguments;
        $this->outputInterface = $outputInterface;
        $this->environment = $environment;

        $this->git = $git;
        $this->msgfmt = new Msgfmt();
    }

    public function run(): int
    {
        chdir($this->workingDir);

        // Version command is always available even if application is not configured
        if ($this->arguments->version) {
            $this->getPackageVersion();
            return 0;
        }

        if ($prepareApplicationExitCode = $this->prepareApplication() != 0) {
            return $prepareApplicationExitCode;
        }

        if ($this->arguments->weblatePull) {
            $this->pullWeblateComponents($this->config);
            return 0;
        }

        if ($this->arguments->justParse) {
            $this->parseSources();
            return 0;
        }

        if ($this->arguments->check) {
            $this->listAllUntranslatedStringsFromTheCurrentBranch();
            return 0;
        }

        if ($this->arguments->prune) {
            $this->prune();
            return 0;
        }

        $this->commitAndPushWeblateComponents();
        $translationFiles = $this->translationFileManager->getAllTranslationFiles();
        // I'm not sure if we need this step
//        $this->addNewTranslationFiles(array_filter($translationFiles, fn($translationFile) => $translationFile->isNew));
        $this->downloadTranslations($translationFiles);

        $codeParseResult = $this->parseSources();
        if ($codeParseResult->newStrings > 0) {
            $this->outputInterface->warning("i18n_mrg found $codeParseResult->newStrings new strings which are going to be pushed to weblate now. Should we continue? (Y/n)");

            $confirmation = $this->arguments->autoconfirm ? 'Y' : readline();
            if (!($confirmation === 'Y' || $confirmation === 'y')) {
                $this->outputInterface->error("Aborting");
                return 0;
            }
        }

        $this->repositoryManager->pushToVcs($codeParseResult->affectedTranslationFiles);

        $this->pullWeblateComponents($this->config);
        $this->downloadTranslations($codeParseResult->affectedTranslationFiles);
        $this->listAllUntranslatedStringsFromTheCurrentBranch();

        return 0;
    }

    private function prepareApplication(): int
    {
        $factory = new ApplicationFactory(
            $this->workingDir,
            $this->environment,
            $this->outputInterface
        );

        if (!$this->checkSystemRequirements()) {
            return 1;
        }

        if (!$this->isWorkingDirHasConfig()) {
            return 1;
        }

        $this->config = $factory->readConfig();
        if ($this->config === null) {
            return 1;
        }

        $this->vcsApi = $factory->buildVcsApiClient($this->config);
        if (!$this->vcsApi) {
            return 1;
        }

        $this->weblateApi = $factory->buildWeblateApiClient($this->arguments, $this->config);
        $this->translationFileManager = $factory->buildTranslationFileManager($this->config, $this->arguments);
        $this->repositoryManager = $factory->buildRepositoryManager($this->vcsApi);
        $this->codeParser = $factory->buildCodeParser($this->config, $this->arguments);

        return 0;
    }

    private function getPackageVersion()
    {
        $composerJsonFactory = new ComposerJsonFactory();
        $this->outputInterface->info($composerJsonFactory->read()->version);
    }

    private function checkSystemRequirements(): bool
    {
        if (!$this->git->isInstalled()) {
            $this->outputInterface->critical('git is not installed, but required to use i18n_mrg');
            return false;
        }

        if (!$this->msgfmt->isInstalled()) {
            $this->outputInterface->critical('msgfmt is not installed, but required to use i18n_mrg');
            return false;
        }

        return true;
    }

    private function isWorkingDirHasConfig(): bool
    {
        $configFileName = $this->workingDir.'/.translate-config.json';
        if (!file_exists($configFileName)) {
            $this->outputInterface->critical("Can't find translation config at ".$configFileName."! The application will terminate");
            return false;
        }

        return true;
    }

    private function pullWeblateComponents(Config $config)
    {
        foreach ($config->components as $component) {
            $this->outputInterface->info("Pulling the Weblate component {$component->weblateProjectSlug}/{$component->weblateComponentSlug}...");
            $this->weblateApi->pullComponent($component->weblateProjectSlug, $component->weblateComponentSlug);
        }
    }

    private function commitAndPushWeblateComponents()
    {
        foreach ($this->config->components as $component) {
            $this->outputInterface->info("Committing the current state of Weblate component {$component->weblateProjectSlug}/{$component->weblateComponentSlug}...");
            $this->weblateApi->commitComponent($component->weblateProjectSlug, $component->weblateComponentSlug);

            $this->outputInterface->info("Pushing the current state of Weblate component {$component->weblateProjectSlug}/{$component->weblateComponentSlug}...");
            $this->weblateApi->pushComponent($component->weblateProjectSlug, $component->weblateComponentSlug);
        }
    }

    private function listAllUntranslatedStringsFromTheCurrentBranch()
    {
        $branchName = $this->git->getCurrentBranchName();
        $this->translationFileManager->listAllUntranslatedStringsFromTheCurrentBranch($branchName);
    }

    private function downloadTranslations(array $translationFiles)
    {
        $weblateManager = new WeblateManager(
            $this->outputInterface,
            $this->weblateApi,
            $this->config
        );

        $weblateManager->downloadTranslations($translationFiles);
    }

    private function parseSources(): CodeParseResult
    {
        return $this->codeParser->parseSources(
            $this->git->getCurrentBranchName()
        );
    }

    private function prune()
    {
        if ($this->git->getCurrentBranchName() !== 'master' && $this->git->getCurrentBranchName() !== 'main') {
            $this->outputInterface->error("The --prune argument could be run from master branch only! Please consider running: ".PHP_EOL."git checkout master");
            return;
        }

        $this->outputInterface->info("Are you sure that you merged all the currently active feature branches in master?");
        $confirmation = $this->arguments->autoconfirm ? 'Y' : readline();
        if (!($confirmation === 'Y' || $confirmation === 'y')) {
            $this->outputInterface->error("You must merge all the current feature branches in master before running --prune command!");
            return;
        }

        $this->commitAndPushWeblateComponents();

        $translationFiles = $this->translationFileManager->getAllTranslationFiles();
        $this->downloadTranslations($translationFiles);

        $totalDisabledStrings = $this->translationFileManager->disableTranslations($translationFiles);

        $this->repositoryManager->pushToVcs($translationFiles);
        $this->pullWeblateComponents($this->config);

        $this->outputInterface->success("Strings which are not used in the project are now marked as disabled. There were $totalDisabledStrings of them in the project.");
    }

    private function addNewTranslationFiles(array $newTranslationFiles)
    {
        // I'm not sure if we need it
        foreach ($newTranslationFiles as $translationFile) {
            $this->outputInterface->info("No translation file found for {$translationFile->weblateCode}. Creating a new one...");

//            $this->addLanguage($locale->localeName, $translationFile);
        }
    }
}
