<?php

namespace TranslationMergeTool\WeblateAPI;


use GuzzleHttp\Client;
use TranslationMergeTool\Config\Config;

class WeblateAPI implements IWeblateAPI
{
    private Client $httpClient;
    private string $authToken;

    public function __construct(Config $config)
    {
        $this->authToken = $config->weblateAuthToken;

        $this->httpClient = new Client([
            'base_uri' => $config->weblateServiceUrl.'/api/',
        ]);
    }

    /**
     * curl \
     * -d operation=commit \
     * -H "Authorization: Token token" \
     * http://159.65.200.211/api/components/crm/translate/repository/
     */
    public function commitComponent(string $projectSlug, string $componentSlug) {
        $this->httpClient->post(
            "components/{$projectSlug}/{$componentSlug}/repository/",
            [
                'multipart' => [
                    [
                        'name' => 'operation',
                        'contents' => 'commit',
                    ]
                ],
                'headers' => [
                    'Authorization' => 'Token '.$this->authToken,
                ]
            ]
        );
    }

    /**
     * curl \
     * -d operation=push \
     * -H "Authorization: Token token" \
     * http://159.65.200.211/api/components/crm/translate/repository/
     */
    public function pushComponent(string $projectSlug, string $componentSlug) {
        $this->httpClient->post(
            "components/{$projectSlug}/{$componentSlug}/repository/",
            [
                'multipart' => [
                    [
                        'name' => 'operation',
                        'contents' => 'push',
                    ]
                ],
                'headers' => [
                    'Authorization' => 'Token '.$this->authToken,
                ]
            ]
        );
    }

    /**
     * curl \
     * -d operation=pull \
     * -H "Authorization: Token token" \
     * http://159.65.200.211/api/components/crm/translate/repository/
     */
    public function pullComponent(string $projectSlug, string $componentSlug) {
        $this->httpClient->post(
            "components/{$projectSlug}/{$componentSlug}/repository/",
            [
                'multipart' => [
                    [
                        'name' => 'operation',
                        'contents' => 'pull',
                    ]
                ],
                'headers' => [
                    'Authorization' => 'Token '.$this->authToken,
                ]
            ]
        );
    }

    /**
     * curl -X GET \
     * -H "Authorization: Token token" \
     * -o download.po \
     * http://159.65.200.211/api/translations/crm/translate/tr/file/
     * @param string $localeName
     * @return string
     */
    public function downloadTranslationFile(string $projectSlug, string $componentSlug, string $localeName): string
    {
        $result = $this->httpClient->get(
            "translations/{$projectSlug}/{$componentSlug}/$localeName/file/",
            [
                'headers' => [
                    'Authorization' => 'Token '.$this->authToken,
                ]
            ]
        );

        return $result->getBody()->getContents();
    }
}
