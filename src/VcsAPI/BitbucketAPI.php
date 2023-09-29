<?php

namespace TranslationMergeTool\VcsAPI;


use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;
use TranslationMergeTool\Exceptions\ConfigValidation\NoAuthCredentialsException;

class BitbucketAPI extends VcsApiAbstract implements IVcsApi
{
    protected $baseUri = 'https://api.bitbucket.org/2.0/';

    /**
     * @return ResponseInterface
     *
     * Request example:
     * curl -X POST https://api.bitbucket.org/2.0/repositories/nevidimov/giftd-crm/src \
     * -H "Authorization: Bearer <token>" \
     * -F resources/lang/i18n/tr_TR/LC_MESSAGES/default.po=@resources/lang/i18n/tr_TR/LC_MESSAGES/default.po \
     * -F branch=translation-test \
     * -i
     */
    public function commit(): ResponseInterface
    {

        $response = null;
        foreach ($this->fileList as $translationFile) {
            $response = $this->httpClient->post(
                "repositories/{$this->config->vcsRepository}/src",
                [
                    'multipart' => [
                        [
                            'name' => 'branch',
                            'contents' => $this->config->translationBranchName,
                        ],
                        [
                            'name' => $translationFile->relativePath,
                            'contents' => file_get_contents($translationFile->absolutePath),
                        ]
                    ],
                ],
            );
        }

        return $response;
    }

    function createHttpClient(): Client
    {
        return new Client([
            'base_uri' => $this->baseUri,
            'headers' => [
                'Authorization' => 'Bearer ' . $this->config->bitbucketAccessToken
            ]
        ]);
    }

    protected function validateConfig(): void
    {
        if (!$this->config->bitbucketAccessToken) {
            throw new NoAuthCredentialsException();
        }
    }

    public function getProviderName(): string
    {
        return "Bitbucket";
    }
}
