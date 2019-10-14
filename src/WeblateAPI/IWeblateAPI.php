<?php

namespace TranslationMergeTool\WeblateAPI;

interface IWeblateAPI
{
    public function commitComponent();

    public function pushComponent();

    /**
     *
     *
     * curl \
     * -d operation=pull \
     * -H "Authorization: Token token" \
     * http://159.65.200.211/api/components/crm/translate/repository/
     */
    public function pullComponent();

    /**
     *
     *
     * curl -X GET \
     * -H "Authorization: Token token" \
     * -o download.po \
     * http://159.65.200.211/api/translations/crm/translate/tr/file/
     * @param string $localeName
     */
    public function downloadTranslation(string $localeName);
}