<?php
/**
 * Created by PhpStorm.
 * User: lehadnk
 * Date: 2018-12-05
 * Time: 16:43
 */

namespace UnitTests\Config;

use UnitTests\AbstractCase;

class ConfigFactoryTest extends AbstractCase
{

    public function testAddNewTranslations()
    {
        $config = $this->getTestConfig();

        $this->assertEquals('1.2.0', $config->configVersion);
        $this->assertEquals('bitbucket', $config->vcs);
        $this->assertEquals('test-bitbucket-username', $config->bitbucketUsername);
        $this->assertEquals('test-bitbucket-password', $config->bitbucketPassword);
        $this->assertEquals('test/repository', $config->vcsRepository);

        $this->assertEquals('translation', $config->translationBranchName);

        $this->assertEquals('http://weblate-test-server.local', $config->weblateServiceUrl);
        $this->assertEquals('crm', $config->weblateProjectSlug);
        $this->assertEquals('main', $config->weblateComponentSlug);
        $this->assertEquals('test-weblate-auth-token', $config->weblateAuthToken);

        $this->assertEquals(1, count($config->components));
        $this->assertEquals('default', $config->components[0]->name);
        $this->assertEquals('translations/{localeName}/translation.po', $config->components[0]->translationFileName);
        $this->assertEquals(2, count($config->components[0]->includePaths));
        $this->assertEquals("src/", $config->components[0]->includePaths[0]);
        $this->assertEquals("public/SomeFileOutsideOfIncludeDir.php", $config->components[0]->includePaths[1]);
        $this->assertEquals(1, count($config->components[0]->excludePaths));
        $this->assertEquals("src/excludedDirectory", $config->components[0]->excludePaths[0]);
    }
}
