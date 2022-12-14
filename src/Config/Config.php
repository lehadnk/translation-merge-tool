<?php
/**
 * Created by PhpStorm.
 * User: lehadnk
 * Date: 10/7/18
 * Time: 2:00 PM
 */

namespace TranslationMergeTool\Config;

use PhpJsonMarshaller\Annotations\MarshallProperty;

class Config
{
    /**
     * @MarshallProperty(name="configVersion", type="string")
     */
    public $configVersion;

    /**
     * @MarshallProperty(name="vcs", type="string")
     * @var Component[]
     */
    public $vcs;

    /**
     * @MarshallProperty(name="vcsUsername", type="string")
     */
    public $vcsUsername;

    /**
     * @MarshallProperty(name="vcsPassword", type="string")
     */
    public $vcsPassword;

    /**
     * @MarshallProperty(name="vcsRepository", type="string")
     */
    public $vcsRepository;

    /**
     * @MarshallProperty(name="translationBranchName", type="string")
     */
    public $translationBranchName;

    /**
     * @MarshallProperty(name="weblateServiceUrl", type="string")
     */
    public $weblateServiceUrl;

    /**
     * @MarshallProperty(name="weblateProjectSlug", type="string")
     */
    public $weblateProjectSlug;

    /**
     * @MarshallProperty(name="weblateComponentSlug", type="string")
     */
    public $weblateComponentSlug;

    /**
     * @MarshallProperty(name="weblateAuthToken", type="string")
     */
    public $weblateAuthToken;

    /**
     * @MarshallProperty(name="components", type="\TranslationMergeTool\Config\Component[]")
     * @var Component[]
     */
    public $components;

    /**
     * @MarshallProperty(name="outputJson", type="bool")
     */
    public $outputJson;

    /**
     * @MarshallProperty(name="vcsHostName", type="string")
     */
    public $vcsHostName;

    /**
     * @var string?
     */
    public $githubAuthToken;

    /**
     * @var string?
     */
    public $gitlabAuthToken;
}
