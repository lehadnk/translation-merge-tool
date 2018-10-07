<?php
/**
 * Created by PhpStorm.
 * User: lehadnk
 * Date: 10/7/18
 * Time: 2:32 PM
 */

namespace TranslationMergeTool;


use GuzzleHttp\Client;
use TranslationMergeTool\Config\Config;

class BitbucketAPI
{
    /**
     * @var Client
     */
    private $httpClient;

    public function __construct(Config $config)
    {
        $this->httpClient = new Client([
            'base_uri' => 'https://api.bitbucket.org/2.0/',
            'auth' => [$config->bitbucketUsername, $config->bitbucketPassword],
        ]);
    }

    /**
     * @param string $remoteFileName
     * @param string $fileName
     * @param string $branchName
     *
     * Request example:
     * curl -u lehadnk@gmail.com:7e4y2ad2 \
     * -X POST https://api.bitbucket.org/2.0/repositories/nevidimov/giftd-crm/src \
     * -F resources/lang/i18n/tr_TR/LC_MESSAGES/default.po=@resources/lang/i18n/tr_TR/LC_MESSAGES/default.po \
     * -F branch=translation-test \
     * -i
     */
    public function pushFile(string $remoteFileName, string $fileName, string $branchName) {
        $this->httpClient->post(
            'repositories/nevidimov/giftd-crm/src',
            [
                'multipart' => [
                    [
                        'name' => 'branch',
                        'contents' => 'translation-test',
                    ],
                    [
                        'name' => 'resources/lang/i18n/tr_TR/LC_MESSAGES/default.po',
                        'contents' => file_get_contents('/Users/lehadnk/work/giftd/crm/resources/lang/i18n/tr_TR/LC_MESSAGES/default.po'),
                    ]
                ],
            ]
        );
    }
}