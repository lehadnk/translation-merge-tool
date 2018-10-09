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

class WeblateAPI
{
    /**
     * @var string
     */
    private $authToken;

    /**
     * @var string
     */
    private $projectSlug;

    /**
     * @var string
     */
    private $componentSlug;

    /**
     * @var Client
     */
    private $httpClient;

    /**
     * @var string
     */
    private $token;

    public function __construct(Config $config)
    {
        $this->authToken = $config->weblateAuthToken;
        $this->projectSlug = $config->weblateProjectSlug;
        $this->token = $config->weblateAuthToken;
        $this->componentSlug = $config->weblateComponentSlug;

        $this->httpClient = new Client([
            'base_uri' => $config->weblateServiceUrl.'/api/',
        ]);
    }

    /**
     *
     *
     * curl \
     * -d operation=pull \
     * -H "Authorization: Token token" \
     * http://159.65.200.211/api/components/crm/translate/repository/
     */
    public function pullComponent() {
        $this->httpClient->post(
            "components/{$this->projectSlug}/{$this->componentSlug}/repository/",
            [
                'multipart' => [
                    [
                        'name' => 'operation',
                        'contents' => 'pull',
                    ]
                ],
                'headers' => [
                    'Authorization' => 'Token '.$this->token,
                ]
            ]
        );
    }

    /**
     *
     *
     * curl -X GET \
     * -H "Authorization: Token token" \
     * -o download.po \
     * http://159.65.200.211/api/translations/crm/translate/tr/file/
     */
    public function downloadTranslation(string $localeName)
    {
        $result = $this->httpClient->get(
            "translations/{$this->projectSlug}/{$this->componentSlug}/$localeName/file",
            [
                'headers' => [
                    'Authorization' => 'Token '.$this->token,
                ]
            ]
        );

        return $result->getBody()->getContents();
    }
}