<?php
/**
 * Created by PhpStorm.
 * User: lehadnk
 * Date: 30/10/2018
 * Time: 20:03
 */

namespace TranslationMergeTool\VcsAPI;

use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use Psr\Http\Message\ResponseInterface;
use TranslationMergeTool\Console;
use TranslationMergeTool\Exceptions\ConfigValidation\NoAuthTokenException;

class GitlabAPI extends VcsApiAbstract implements IVcsApi
{
    /**
     * @var string
     */
    protected $baseUri = 'https://gitlab.com/api/v4/';

    protected function validateConfig(): void
    {
        if (empty($this->config->gitlabAuthToken)) {
            throw new NoAuthTokenException();
        }
    }

    //

    /**
     * Request URI example: https://gitlab.com/api/v4/projects/giftd%2Fcrm/repository/commits
     * @return ResponseInterface
     */
    public function commit():ResponseInterface
    {
        $slug = urlencode($this->config->vcsRepository);

        Console::debug("Using gitlab auth token {$this->config->gitlabAuthToken}...");

        $actions = [];
        foreach ($this->fileList as $translationFile) {
            $actions[] = [
                'action' => $translationFile->isNew ? 'create' : 'update',
                'file_path' => $translationFile->relativePath,
                'content' => file_get_contents($translationFile->absolutePath),
            ];
        }

        $response = $this->httpClient->post(
            "projects/$slug/repository/commits",
            [
                RequestOptions::HEADERS => [
                    'PRIVATE-TOKEN' => $this->config->gitlabAuthToken,
                ],
                RequestOptions::JSON => [
                    'branch' => $this->config->translationBranchName,
                    'commit_message' => 'The commit was made by i18n_mrg tool',
                    'actions' => $actions
                ],
            ]
        );

        return $response;
    }

    /**
     * @return Client
     */
    function createHttpClient(): Client
    {
        $this->baseUri = $this->config->vcsHostName ?? "https://gitlab.com";
        $this->baseUri .= "/api/v4/";

        return new Client([
            'base_uri' => $this->baseUri,
        ]);
    }
}
