<?php
/**
 * Created by PhpStorm.
 * User: lehadnk
 * Date: 10/7/18
 * Time: 2:32 PM
 */

namespace TranslationMergeTool;


use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use TranslationMergeTool\Config\Config;

class BitbucketAPI
{
    /**
     * @var Client
     */
    private $httpClient;

    /**
     * @var Config
     */
    private $config;

    public function __construct(Config $config)
    {
        $this->httpClient = new Client([
            'base_uri' => 'https://api.bitbucket.org/2.0/',
            'auth' => [$config->bitbucketUsername, $config->bitbucketPassword],
        ]);
        $this->config = $config;
    }

    /**
     * @param string $remoteFileName
     * @param string $fileName
     * @param string $branchName
     * @return ResponseInterface
     *
     * Request example:
     * curl -u lehadnk@gmail.com:password \
     * -X POST https://api.bitbucket.org/2.0/repositories/nevidimov/giftd-crm/src \
     * -F resources/lang/i18n/tr_TR/LC_MESSAGES/default.po=@resources/lang/i18n/tr_TR/LC_MESSAGES/default.po \
     * -F branch=translation-test \
     * -i
     */
    public function pushFile(string $remoteFileName, string $fileName) {
        $response = $this->httpClient->post(
            "repositories/{$this->config->bitbucketRepository}/src",
            [
                'multipart' => [
                    [
                        'name' => 'branch',
                        'contents' => $this->config->translationBranchName,
                    ],
                    [
                        'name' => $remoteFileName,
                        'contents' => file_get_contents($fileName),
                    ]
                ],
            ]
        );

        return $response;
    }
}