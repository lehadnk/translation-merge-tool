<?php

namespace TranslationMergeTool\Application;

use TranslationMergeTool\CodeParser\CodeParser;
use TranslationMergeTool\Config\Config;
use TranslationMergeTool\Config\ConfigFactory;
use TranslationMergeTool\DTO\Arguments;
use TranslationMergeTool\DTO\Factory\TranslationFileFactory;
use TranslationMergeTool\Environment\Environment;
use TranslationMergeTool\Exceptions\ConfigValidation\ConfigValidationException;
use TranslationMergeTool\Exceptions\ConfigValidation\NoAuthCredentialsException;
use TranslationMergeTool\Exceptions\ConfigValidation\NoAuthTokenException;
use TranslationMergeTool\Output\IOutputInterface;
use TranslationMergeTool\TranslationFile\TranslationFileManager;
use TranslationMergeTool\VcsAPI\IVcsApi;
use TranslationMergeTool\VcsAPI\RepositoryManager;
use TranslationMergeTool\VcsAPI\VcsApiFactory;
use TranslationMergeTool\WeblateAPI\IWeblateAPI;
use TranslationMergeTool\WeblateAPI\MockWeblateAPI;
use TranslationMergeTool\WeblateAPI\WeblateAPI;

class ApplicationFactory
{
    private IOutputInterface $outputInterface;
    private string $workingDir;
    private Environment $environment;

    public function __construct(
        string $workingDir,
        Environment $environment,
        IOutputInterface $outputInterface
    ) {
        $this->workingDir = $workingDir;
        $this->environment = $environment;
        $this->outputInterface = $outputInterface;
    }

    public function readConfig(): ?Config
    {
        $configFileName = $this->workingDir.'/.translate-config.json';
        try {
            $configFactory = new ConfigFactory($this->environment);
            return $configFactory->read($configFileName);
        } catch (ConfigValidationException $e) {
            $this->outputInterface->error($e->getMessage());
            return null;
        }
    }

    public function buildVcsApiClient(Config $config): ?IVcsApi
    {
        try {
            $vcsFactory = new VcsApiFactory($this->outputInterface);
            return $vcsFactory->make($config);
        } catch (NoAuthCredentialsException $ex) {
            $this->outputInterface->error("Error! No {$config->vcs} authentication credentials found.");
            $this->outputInterface->info("Please consider adding the corresponding environment variables into your ~\.bash_profile: https://github.com/lehadnk/translation-merge-tool#handling-authorization-tokens");
        } catch (NoAuthTokenException $ex) {
            $this->outputInterface->error("Error! No {$config->vcs} authentication token found.");
            $this->outputInterface->info("Please consider adding the corresponding environment variable into your ~\.bash_profile: https://github.com/lehadnk/translation-merge-tool#handling-authorization-tokens");
        } catch (ConfigValidationException $ex) {
            $this->outputInterface->error($ex->getMessage());
        }

        return null;
    }

    public function buildWeblateApiClient(Arguments $arguments, Config $config): IWeblateAPI
    {
        if ($arguments->noWeblate) {
            return new MockWeblateAPI($this->outputInterface);
        }

        return new WeblateAPI($config);
    }

    public function buildTranslationFileManager(
        Config $config,
        Arguments $arguments
    ): TranslationFileManager
    {
        return new TranslationFileManager(
            $this->workingDir,
            $config,
            $arguments,
            $this->outputInterface,
            $this->buildCodeParser($config, $arguments),
            new TranslationFileFactory($this->workingDir)
        );
    }

    public function buildRepositoryManager(IVcsApi $vcsApi): RepositoryManager
    {
        return new RepositoryManager($this->outputInterface, $vcsApi);
    }

    public function buildCodeParser(?Config $config, Arguments $arguments)
    {
        return new CodeParser(
            $this->workingDir,
            $config,
            $this->outputInterface,
            new TranslationFileFactory($this->workingDir)
        );
    }
}
