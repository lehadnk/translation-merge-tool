<?php
/**
 * Created by PhpStorm.
 * User: lehadnk
 * Date: 10/7/18
 * Time: 1:40 PM
 */

namespace TranslationMergeTool\Config;

use PhpJsonMarshaller\Decoder\ClassDecoder;
use PhpJsonMarshaller\Exception\JsonDecodeException;
use PhpJsonMarshaller\Exception\UnknownPropertyException;
use PhpJsonMarshaller\Marshaller\JsonMarshaller;
use PhpJsonMarshaller\Reader\DoctrineAnnotationReader;
use TranslationMergeTool\Exceptions\ConfigValidation\ConfigValidationException;
use TranslationMergeTool\Exceptions\ConfigValidation\ConfigVersionMismatch;

class ConfigFactory
{
    const ACCEPTS_CONFIG_VERSIONS = ['1.1.13'];

    public static function read(string $fileName): Config {
        $contents = file_get_contents($fileName);

        $marshaller = new JsonMarshaller(new ClassDecoder(new DoctrineAnnotationReader()));
        /**
         * @var $config Config
         */
        try {
            $config = $marshaller->unmarshall($contents, Config::class);
        } catch (JsonDecodeException $e) {
            throw new ConfigValidationException(".translate-config.json is corrupt. Please make sure that it's correct json structure.");
        } catch (UnknownPropertyException $e) {
            throw new ConfigValidationException(".translate-config.json is lacking for some mandatory fields. Please make sure that the config structure is fully valid.");
        }

        if (!self::isConfigVersionAccepted($config->configVersion)) {
            throw new ConfigVersionMismatch("The tool accepts only config versions ".implode(',', self::ACCEPTS_CONFIG_VERSIONS)." while config version is {$config->configVersion}. You must either update tool or config");
        }

        $config->gitlabAuthToken = getenv('I18N_MRG_GITLAB_AUTH_TOKEN');
        $config->githubAuthToken = getenv('I18N_MRG_GITHUB_AUTH_TOKEN');
        $config->vcsUsername = $config->vcsUsername ?? getenv('I18N_MRG_BITBUCKET_USERNAME');
        $config->vcsPassword = $config->vcsPassword ?? getenv('I18N_MRG_BITBUCKET_PASSWORD');
        $config->weblateAuthToken = $config->weblateAuthToken ?? getenv('I18N_WEBLATE_AUTH_TOKEN');

        return $config;
    }

    private static function isConfigVersionAccepted(string $versionTag): bool
    {
        return in_array($versionTag, self::ACCEPTS_CONFIG_VERSIONS);
    }
}