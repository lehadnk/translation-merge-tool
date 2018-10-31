<?php
/**
 * Created by PhpStorm.
 * User: lehadnk
 * Date: 30/10/2018
 * Time: 20:07
 */

namespace TranslationMergeTool\API;


use GuzzleHttp\Client;
use TranslationMergeTool\Config\Config;

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

    public function __construct(Config $config)
    {
        $this->config = $config;
        $this->httpClient = $this->createHttpClient();
    }

    abstract function createHttpClient(): Client;

    public function addFile(string $fileName, string $remoteFileName)
    {
        $this->fileList[$fileName] = $remoteFileName;
    }
}