<?php


namespace TranslationMergeTool\WeblateAPI;


class MockWeblateAPI implements IWeblateAPI
{

    public function commitComponent()
    {
        echo "Commiting weblate component...".PHP_EOL;
    }

    public function pushComponent()
    {
        echo "Pushing weblate component".PHP_EOL;
    }

    /**
     *
     *
     * curl \
     * -d operation=pull \
     * -H "Authorization: Token token" \
     * http://159.65.200.211/api/components/crm/translate/repository/
     */
    public function pullComponent()
    {
        echo "Pulling weblate component".PHP_EOL;
    }

    /**
     *
     *
     * curl -X GET \
     * -H "Authorization: Token token" \
     * -o download.po \
     * http://159.65.200.211/api/translations/crm/translate/tr/file/
     * @param string $localeName
     * @return string
     */
    public function downloadTranslation(string $localeName)
    {
        echo "Downloading translation file $localeName".PHP_EOL;
        return file_get_contents(__DIR__.'/../../tests/project/translations/ru/translation.po');
    }
}