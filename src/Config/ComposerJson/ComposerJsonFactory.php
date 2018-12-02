<?php
/**
 * Created by PhpStorm.
 * User: lehadnk
 * Date: 10/13/18
 * Time: 5:06 AM
 */

namespace TranslationMergeTool\Config\ComposerJson;


use PhpJsonMarshaller\Decoder\ClassDecoder;
use PhpJsonMarshaller\Marshaller\JsonMarshaller;
use PhpJsonMarshaller\Reader\DoctrineAnnotationReader;

class ComposerJsonFactory
{
    /**
     * @return ComposerJson
     * @throws \Doctrine\Common\Annotations\AnnotationException
     * @throws \PhpJsonMarshaller\Exception\JsonDecodeException
     * @throws \PhpJsonMarshaller\Exception\UnknownPropertyException
     */
    public static function read(): ComposerJson
    {
        $contents = file_get_contents(__DIR__ . '/../../../composer.json');

        $marshaller = new JsonMarshaller(new ClassDecoder(new DoctrineAnnotationReader()));
        $composerJson = $marshaller->unmarshall($contents, ComposerJson::class);

        return $composerJson;
    }
}