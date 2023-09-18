<?php

namespace TranslationMergeTool\VcsAPI;


use GuzzleHttp\Client;
use TranslationMergeTool\Config\Config;
use TranslationMergeTool\DTO\TranslationFile;
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
     * @var TranslationFile[]
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

    public function addFile(TranslationFile $translationFile)
    {
        $this->fileList[] = $translationFile;
    }

    /**
     * @throws NoAuthTokenException
     * @throws NoAuthCredentialsException
     * @throws ConfigValidationException
     */
    abstract protected function validateConfig(): void;
}
