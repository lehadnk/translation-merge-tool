<?php
/**
 * Created by PhpStorm.
 * User: lehadnk
 * Date: 10/7/18
 * Time: 2:32 PM
 */

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
     * curl -u lehadnk@gmail.com:password \
     * -X POST https://api.bitbucket.org/2.0/repositories/nevidimov/giftd-crm/src \
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
                    ]
                ]
            );
        }

        return $response;
    }

    function createHttpClient(): Client
    {
        return new Client([
            'base_uri' => $this->baseUri,
            'auth' => [$this->config->bitbucketUsername, $this->config->bitbucketPassword],
        ]);
    }

    protected function validateConfig(): void
    {
        if (!$this->config->bitbucketUsername || !$this->config->bitbucketPassword) {
            throw new NoAuthCredentialsException();
        }
    }
}
