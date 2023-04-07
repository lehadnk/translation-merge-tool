<?php
/**
 * Created by PhpStorm.
 * User: lehadnk
 * Date: 10/7/18
 * Time: 2:00 PM
 */

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
    public string $weblateProjectSlug;
    public string $weblateComponentSlug;
    public string $weblateAuthToken;
    public bool $outputJson = false;

    /**
     * @var Component[]
     */
    public $components;
}
