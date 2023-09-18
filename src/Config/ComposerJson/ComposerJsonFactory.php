<?php

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
