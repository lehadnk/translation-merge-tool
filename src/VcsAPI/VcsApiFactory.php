<?php

namespace TranslationMergeTool\VcsAPI;


use splitbrain\phpcli\Exception;
use TranslationMergeTool\Config\Config;
use TranslationMergeTool\Exceptions\ConfigValidation\ConfigValidationException;
use TranslationMergeTool\Exceptions\ConfigValidation\NoAuthCredentialsException;
use TranslationMergeTool\Exceptions\ConfigValidation\NoAuthTokenException;
use TranslationMergeTool\Output\IOutputInterface;

class VcsApiFactory
{
    private IOutputInterface $outputInterface;

    public function __construct(
        IOutputInterface $outputInterface
    ) {
        $this->outputInterface = $outputInterface;
    }

    /**
     * @param Config $config
     * @return IVcsApi
     * @throws ConfigValidationException
     * @throws NoAuthTokenException
     * @throws NoAuthCredentialsException
     */
    public function make(Config $config): IVcsApi
    {
        if ($config->vcs === 'bitbucket') {
            return new BitbucketAPI($config);
        }
        if ($config->vcs === 'gitlab') {
            return new GitlabAPI($config, $this->outputInterface);
        }
        if ($config->vcs === 'github') {
            return new GithubAPI($config);
        }
        if ($config->vcs === 'mock') {
            return new MockVcsAPI($this->outputInterface);
        }
        throw new Exception("No API class found for {$config->vcs}");
    }
}
