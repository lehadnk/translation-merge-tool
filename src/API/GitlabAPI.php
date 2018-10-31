<?php
/**
 * Created by PhpStorm.
 * User: lehadnk
 * Date: 30/10/2018
 * Time: 20:03
 */

namespace TranslationMergeTool\API;

use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use Psr\Http\Message\ResponseInterface;

class GitlabAPI extends VcsApiAbstract implements VcsApiInterface
{
    protected $baseUri = 'https://gitlab.com/api/v4/';

    //https://gitlab.com/api/v4/projects/giftd%2Fcrm/repository/commits
    public function commit():ResponseInterface
    {
        $slug = urlencode($this->config->vcsRepository);

        $actions = [];
        foreach ($this->fileList as $fileName => $remoteFileName) {
            $actions[] = [
                'action' => 'update',
                'file_path' => $remoteFileName,
                'content' => file_get_contents($fileName),
            ];
        }

        $response = $this->httpClient->post(
            "projects/$slug/repository/commits",
            [
                RequestOptions::HEADERS => [
                    'PRIVATE-TOKEN' => $this->config->vcsAuthToken,
                ],
                RequestOptions::JSON => [
                    'branch' => $this->config->translationBranchName,
                    'commit_message' => 'The commit was made by using i18n_mrg tool',
                    'actions' => $actions
                ],
            ]
        );

        return $response;
    }

    function createHttpClient(): Client
    {
        return new Client([
            'base_uri' => $this->baseUri,
        ]);
    }
}