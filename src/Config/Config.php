<?php

namespace TranslationMergeTool\Config;

class Config
{
    public string $configVersion;
    public string $vcs;
    public ?string $vcsHostName = null;
    public ?string $bitbucketUsername = null;
    public ?string $bitbucketPassword = null;
    public string $vcsRepository;
    public ?string $gitlabAuthToken = null;
    public ?string $githubAuthToken = null;
    public string $translationBranchName;
    public string $weblateServiceUrl;
    public string $weblateAuthToken;
    public bool $outputJson = false;

    /**
     * @var Component[]
     */
    public $components;
}
