<?php
/**
 * Created by PhpStorm.
 * User: lehadnk
 * Date: 31/10/2018
 * Time: 21:00
 */

namespace TranslationMergeTool\API;


use splitbrain\phpcli\Exception;
use TranslationMergeTool\Config\Config;

class VcsApiFactory
{
    public static function make(Config $config): VcsApiInterface
    {
        if ($config->vcs === 'bitbucket') {
            return new BitbucketAPI($config);
        }
        if ($config->vcs === 'gitlab') {
            return new GitlabAPI($config);
        }
        throw new Exception("No API class found for {$config->vcs}");
    }
}