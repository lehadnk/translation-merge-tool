<?php
/**
 * Created by PhpStorm.
 * User: lehadnk
 * Date: 2018-11-28
 * Time: 17:35
 */

namespace UnitTests;

use TranslationMergeTool\Config\Component;
use TranslationMergeTool\Config\Config;
use TranslationMergeTool\Config\ConfigFactory;
use PHPUnit\Framework\TestCase;
use TranslationMergeTool\Environment\Environment;

abstract class AbstractCase extends TestCase
{
    protected Config $config;

    protected function setUp(): void
    {
        $this->config = $this->getTestConfig();
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
            'test-bitbucket-username',
            'test-bitbucket-password',
            'test-weblate-auth-token',
        );
    }

    protected function getTestConfigPath(): string
    {
        return $this->getTestProjectDir().'/.translate-config.json';
    }

    protected function getTestProjectDir()
    {
        return __DIR__.'/../project';
    }

    protected function getTestComponent(): Component
    {
        return $this->config->components[0];
    }
}
