<?php


namespace TranslationMergeTool\VcsAPI;


use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use Psr\Http\Message\ResponseInterface;
use TranslationMergeTool\Exceptions\ConfigValidation\ConfigValidationException;
use TranslationMergeTool\Exceptions\ConfigValidation\NoAuthTokenException;

class GithubAPI extends VcsApiAbstract implements IVcsApi
{
    const BASE_URI = 'https://api.github.com/';

    function createHttpClient(): Client
    {
        return new Client([
            'base_uri' => self::BASE_URI,
        ]);
    }

    /**
     * @throws NoAuthTokenException
     * @throws ConfigValidationException
     */
    protected function validateConfig()
    {
        if ($this->config->vcsAuthToken === null) {
            throw new NoAuthTokenException();
        }
    }

    /**
     * @param string $relativePath
     * @return string|null
     */
    private function getFileSha(string $relativePath)
    {
        $response = $this->httpClient->get(
            "repos/{$this->config->vcsRepository}/contents/".$relativePath.'?ref='.$this->config->translationBranchName,
            [
                RequestOptions::HEADERS => [
                    'Authorization' => 'token '.$this->config->vcsAuthToken,
                ],
            ]
        );

        $fileInfo = json_decode($response->getBody());
        $result = $fileInfo->sha ?? null;

        return $result;
    }

    public function commit(): ResponseInterface
    {
        $response = null;
        foreach ($this->fileList as $translationFile) {
            $contents = file_get_contents($translationFile->absolutePath);
            $sha = $this->getFileSha($translationFile->relativePath);

            $response = $this->httpClient->put(
                "repos/{$this->config->vcsRepository}/contents/".$translationFile->relativePath,
                [
                    RequestOptions::HEADERS => [
                        'Authorization' => 'token '.$this->config->vcsAuthToken,
                    ],
                    RequestOptions::JSON => [
                        'message' => 'The commit was made by using i18n_mrg tool',
                        'content' => base64_encode($contents),
                        'branch' => $this->config->translationBranchName,
                        'sha' => $sha,
                    ],
                ]
            );
        }

        return $response;
    }
}