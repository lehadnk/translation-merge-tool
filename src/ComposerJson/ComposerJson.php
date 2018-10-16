<?php
/**
 * Created by PhpStorm.
 * User: lehadnk
 * Date: 10/13/18
 * Time: 5:05 AM
 */

namespace TranslationMergeTool\ComposerJson;

use PhpJsonMarshaller\Annotations\MarshallProperty;

class ComposerJson
{
    /**
     * @MarshallProperty(name="version", type="string")
     */
    public $version;
}