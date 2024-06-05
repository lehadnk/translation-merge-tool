<?php

namespace TranslationMergeTool\Config;

use JsonMapper\JsonMapperFactory;
use JsonMapper\JsonMapperInterface;
use TranslationMergeTool\Environment\Environment;
use TranslationMergeTool\Exceptions\ConfigValidation\ConfigVersionMismatch;

class ConfigFactory
{
    const ACCEPTS_CONFIG_VERSIONS = ['1.3.0', '1.4.0'];

    public function __construct(
        private readonly Environment $environment
    ) {
    }

    public function read(string $fileName): Config {
        $config = $this->getJsonMapper()->mapToClassFromString(file_get_contents($fileName), Config::class);

        if (!$this->isConfigVersionAccepted($config->configVersion)) {
            throw new ConfigVersionMismatch("The only accepts config versions of ".implode(', ', self::ACCEPTS_CONFIG_VERSIONS)." while the current config version is {$config->configVersion}. You must either update tool or .translate-config.json.");
        }

        $config->gitlabAuthToken = $this->environment->gitlabAuthToken;
        $config->githubAuthToken = $this->environment->githubAuthToken;
        $config->bitbucketAccessToken = $this->environment->bitbucketAccessToken ?? $config->bitbucketAccessToken;
        $config->weblateAuthToken = $this->environment->weblateAuthToken ?? $config->weblateAuthToken;

        return $config;
    }

    private function isConfigVersionAccepted(string $versionTag): bool
    {
        return in_array($versionTag, self::ACCEPTS_CONFIG_VERSIONS);
    }

    private function getJsonMapper(): JsonMapperInterface
    {
        $mapperFactory = new JsonMapperFactory();
        return $mapperFactory->bestFit();
    }
}
