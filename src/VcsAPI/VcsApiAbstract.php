<?php
/**
 * Created by PhpStorm.
 * User: lehadnk
 * Date: 30/10/2018
 * Time: 20:07
 */

namespace TranslationMergeTool\VcsAPI;


use GuzzleHttp\Client;
use TranslationMergeTool\Config\Config;
use TranslationMergeTool\Exceptions\ConfigValidation\ConfigValidationException;
use TranslationMergeTool\Exceptions\ConfigValidation\NoAuthCredentialsException;
use TranslationMergeTool\Exceptions\ConfigValidation\NoAuthTokenException;

abstract class VcsApiAbstract
{
    /**
     * @var Client
     */
    protected $httpClient;

    /**
     * @var Config
     */
    protected $config;

    /**
     * @var string[]
     */
    protected $fileList = [];

    /**
     * @var string
     */
    protected $baseUri;

    /**
     * VcsApiAbstract constructor.
     * @param Config $config
     *
     * @throws NoAuthTokenException
     * @throws NoAuthTokenException
     * @throws ConfigValidationException
     */
    public function __construct(Config $config)
    {
        $this->config = $config;
        $this->validateConfig();
        $this->httpClient = $this->createHttpClient();
    }

    abstract function createHttpClient(): Client;

    public function addFile(string $fileName, string $remoteFileName)
    {
        $this->fileList[$fileName] = $remoteFileName;
    }

    /**
     * @throws NoAuthTokenException
     * @throws NoAuthCredentialsException
     * @throws ConfigValidationException
     */
    abstract protected function validateConfig();
}