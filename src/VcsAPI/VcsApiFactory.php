<?php
/**
 * Created by PhpStorm.
 * User: lehadnk
 * Date: 31/10/2018
 * Time: 21:00
 */

namespace TranslationMergeTool\VcsAPI;


use splitbrain\phpcli\Exception;
use TranslationMergeTool\Config\Config;
use TranslationMergeTool\Exceptions\ConfigValidation\ConfigValidationException;
use TranslationMergeTool\Exceptions\ConfigValidation\NoAuthCredentialsException;
use TranslationMergeTool\Exceptions\ConfigValidation\NoAuthTokenException;

class VcsApiFactory
{
    /**
     * @param Config $config
     * @return IVcsApi
     * @throws ConfigValidationException
     * @throws NoAuthTokenException
     * @throws NoAuthCredentialsException
     */
    public static function make(Config $config): IVcsApi
    {
        if ($config->vcs === 'bitbucket') {
            return new BitbucketAPI($config);
        }
        if ($config->vcs === 'gitlab') {
            return new GitlabAPI($config);
        }
        if ($config->vcs === 'github') {
            return new GithubAPI($config);
        }
        if ($config->vcs === 'mock') {
            return new MockVcsAPI();
        }
        throw new Exception("No API class found for {$config->vcs}");
    }
}
