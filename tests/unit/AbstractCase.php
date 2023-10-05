<?php

namespace UnitTests;

use PHPUnit\Framework\TestCase;
use TranslationMergeTool\Config\Component;
use TranslationMergeTool\Config\Config;
use TranslationMergeTool\Config\ConfigFactory;
use TranslationMergeTool\Environment\Environment;

abstract class AbstractCase extends TestCase
{
    protected string $testsTmp = __DIR__ . '/../tmp/';
    protected Config $config;

    protected function setUp(): void
    {
        if (is_dir($this->testsTmp)) {
            `rm -rf $this->testsTmp`;
        }
        mkdir($this->testsTmp);

        $from = __DIR__ . '/../resources/basic_project';
        $to = $this->testsTmp . 'basic_project';
        `cp -r $from $to`;

        $from = __DIR__ . '/../resources/monorep_project';
        $to = $this->testsTmp . 'monorep_project';
        `cp -r $from $to`;

        $this->config = $this->getTestConfig();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        `rm -rf $this->testsTmp`;
    }

    protected function getTestConfig(): Config
    {
        $environment = $this->getEnvironment();
        $configFactory = new ConfigFactory($environment);

        return $configFactory->read($this->getTestConfigPath());
    }

    protected function getEnvironment(): Environment
    {
        return new Environment(
            'test-gitlab-auth-token',
            'test-github-auth-token',
            'test-bitbucket-token',
            'test-weblate-auth-token',
        );
    }

    protected function getTestConfigPath(): string
    {
        return $this->getTestProjectDir().'/.translate-config.json';
    }

    protected abstract function getTestProjectDir();

    protected function getTestComponent(): Component
    {
        return $this->config->components[0];
    }
}
