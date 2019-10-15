<?php


namespace TranslationMergeTool\WeblateAPI;


use TranslationMergeTool\Console;

class MockWeblateAPI implements IWeblateAPI
{

    public function commitComponent()
    {
        Console::debug("Commiting weblate component...");
    }

    public function pushComponent()
    {
        Console::debug("Pushing weblate component");
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
        Console::debug("Pulling weblate component");
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
        Console::debug("Downloading translation file $localeName");
        return file_get_contents(__DIR__.'/../../tests/project/translations/ru/translation.po');
    }
}