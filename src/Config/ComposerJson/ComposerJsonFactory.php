<?php
/**
 * Created by PhpStorm.
 * User: lehadnk
 * Date: 10/13/18
 * Time: 5:06 AM
 */

namespace TranslationMergeTool\Config\ComposerJson;

use JsonMapper\JsonMapperFactory;

class ComposerJsonFactory
{
    public function read(): ComposerJson
    {
        $mapperFactory = new JsonMapperFactory();
        $mapper = $mapperFactory->bestFit();

        $contents = file_get_contents(__DIR__ . '/../../../composer.json');
        return $mapper->mapToClassFromString($contents, ComposerJson::class);
    }
}
