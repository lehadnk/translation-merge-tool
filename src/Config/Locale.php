<?php
/**
 * Created by PhpStorm.
 * User: lehadnk
 * Date: 10/7/18
 * Time: 2:08 PM
 */

namespace TranslationMergeTool\Config;

use PhpJsonMarshaller\Annotations\MarshallProperty;

class Locale
{
    /**
     * @MarshallProperty(name="weblateCode", type="string")
     */
    public $weblateCode;

    /**
     * @MarshallProperty(name="localeName", type="string")
     */
    public $localeName;
}