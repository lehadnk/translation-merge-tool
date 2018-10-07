<?php
/**
 * Created by PhpStorm.
 * User: lehadnk
 * Date: 10/7/18
 * Time: 2:20 PM
 */

namespace TranslationMergeTool;


use splitbrain\phpcli\CLI;
use splitbrain\phpcli\Exception;
use splitbrain\phpcli\Options;
use TranslationMergeTool\Config\Config;
use TranslationMergeTool\Config\ConfigFactory;

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

    public function __create(string $workingDir, Config $config) {
        $this->workingDir = $workingDir;
    }

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
        $this->weblateAPI = new WeblateAPI();

        $this->updateTranslations();
    }

    private function updateTranslations()
    {
        $this->info("Updating translation files...");
        $this->info("Pushing updated files to bitbucket...");
        $this->bitbucketAPI->pushFile('1', '2', '3');

        $this->info("Pulling the weblate components...");
        $this->weblateAPI->pullComponent();

        $this->info("Downloading new translation files...");
        $this->weblateAPI->downloadTranslation('tr');
    }
}