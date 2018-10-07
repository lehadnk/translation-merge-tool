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
     * @MarshallProperty(name="bitbucketUsername", type="string")
     */
    public $bitbucketUsername;

    /**
     * @MarshallProperty(name="bitbucketPassword", type="string")
     */
    public $bitbucketPassword;

    /**
     * @MarshallProperty(name="bitbucketRepository", type="string")
     */
    public $bitbucketRepository;

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
     * @MarshallProperty(name="weblateAuthToken", type="string")
     */
    public $weblateAuthToken;

    /**
     * @MarshallProperty(name="locales", type="\TranslationMergeTool\Config\Locale[]")
     * @var Locale[]
     */
    public $locales;

    /**
     * @MarshallProperty(name="components", type="\TranslationMergeTool\Config\Component[]")
     * @var Component[]
     */
    public $components;
}