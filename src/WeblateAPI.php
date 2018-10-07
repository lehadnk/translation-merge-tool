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
     * @var Client
     */
    private $httpClient;

    public function __construct(Config $config)
    {
        $this->authToken = $config->weblateAuthToken;
        $this->projectSlug = $config->weblateProjectSlug;

        $this->httpClient = new Client([
            'base_uri' => $config->weblateServiceUrl.'/api/',
            'auth' => ['Token '.$config->weblateAuthToken],
        ]);
    }

    /**
     *
     *
     * curl \
     * -d operation=pull \
     * -H "Authorization: Token g8XOJcY5Z7UkNbLYOjCl77DpPEXa9yEJ5j4orv7I" \
     * http://159.65.200.211/api/components/crm/translate/repository/
     */
    public function pullComponent() {
        $this->httpClient->get(
            "components/{$this->projectSlug}/translate/repository",
            [
                'multipart' => [
                    [
                        'name' => 'operation',
                        'contents' => 'pull',
                    ]
                ]
            ]
        );
    }

    /**
     *
     *
     * curl -X GET \
     * -H "Authorization: Token g8XOJcY5Z7UkNbLYOjCl77DpPEXa9yEJ5j4orv7I" \
     * -o download.po \
     * http://159.65.200.211/api/translations/crm/translate/tr/file/
     */
    public function downloadTranslation(string $localeName)
    {
        $result = $this->httpClient->get(
            "components/{$this->projectSlug}/translate/$localeName/file"
        );

        return $result->getBody();
    }
}