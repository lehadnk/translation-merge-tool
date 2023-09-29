<?php

namespace UnitTests\Config;

use TranslationMergeTool\Config\ConfigFactory;
use TranslationMergeTool\Environment\Environment;
use UnitTests\AbstractBasicCase;

class ConfigFactoryTest extends AbstractBasicCase
{
    public function testAddNewTranslations()
    {
        $environment = new Environment(
            'test-gitlab-auth-token',
            'test-github-auth-token',
            'test-bitbucket-username',
            'test-bitbucket-password',
            'test-weblate-auth-token',
        );
        $configFactory = new ConfigFactory($environment);
        $config = $configFactory->read($this->getTestConfigPath());

        $this->assertEquals('1.3.0', $config->configVersion);
        $this->assertEquals('bitbucket', $config->vcs);
        $this->assertEquals('test-bitbucket-token', $config->bitbucketAccessToken);
        $this->assertEquals('test/repository', $config->vcsRepository);

        $this->assertEquals('translation', $config->translationBranchName);

        $this->assertEquals('http://weblate-test-server.local', $config->weblateServiceUrl);
        $this->assertEquals('test-weblate-auth-token', $config->weblateAuthToken);

        $this->assertEquals(1, count($config->components));
        $this->assertEquals('default', $config->components[0]->name);
        $this->assertEquals('translations/{localeName}/translation.po', $config->components[0]->translationFileName);
        $this->assertEquals(2, count($config->components[0]->includePaths));
        $this->assertEquals("src/", $config->components[0]->includePaths[0]);
        $this->assertEquals("public/SomeFileOutsideOfIncludeDir.php", $config->components[0]->includePaths[1]);
        $this->assertEquals(1, count($config->components[0]->excludePaths));
        $this->assertEquals("src/excludedDirectory", $config->components[0]->excludePaths[0]);
        $this->assertEquals('crm', $config->components[0]->weblateProjectSlug);
        $this->assertEquals('main', $config->components[0]->weblateComponentSlug);
    }
}
