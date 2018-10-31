<?php
/**
 * Created by PhpStorm.
 * User: lehadnk
 * Date: 10/7/18
 * Time: 1:40 PM
 */

namespace TranslationMergeTool\Config;

use PhpJsonMarshaller\Decoder\ClassDecoder;
use PhpJsonMarshaller\Marshaller\JsonMarshaller;
use PhpJsonMarshaller\Reader\DoctrineAnnotationReader;

class ConfigFactory
{
    public static function read(string $fileName): Config {
        $contents = file_get_contents($fileName);

        $marshaller = new JsonMarshaller(new ClassDecoder(new DoctrineAnnotationReader()));
        /**
         * @var $config Config
         */
        $config = $marshaller->unmarshall($contents, Config::class);

        $config->vcsAuthToken = $config->vcsAuthToken ?? getenv('VCS_AUTH_TOKEN');
        $config->vcsUsername = $config->vcsUsername ?? getenv('VCS_USERNAME');
        $config->vcsPassword = $config->vcsPassword ?? getenv('VCS_PASSWORD');

        return $config;
    }
}