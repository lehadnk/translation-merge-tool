<?php
/**
 * Created by PhpStorm.
 * User: lehadnk
 * Date: 2018-12-03
 * Time: 01:08
 */

namespace TranslationMergeTool\PoReader;


use Gettext\Translation;
use Gettext\Translations;

class PoPostProcessor
{
    public function postProcessPoFile(string $poFileContents): string
    {
        $translations = Translations::fromPoString($poFileContents);
        $translations = $this->removeMalformedTranslations($translations);
        return $translations->toPoString();
    }

    /**
     * This method fixes weird msgfmt behaviour:
     *
     * An example of this weird behaviour:
     * common/i18n/ru_RU/LC_MESSAGES/default.po:20684: inconsistent use of #~
     * msgfmt: too many errors, aborting
     *
     * @param Translations $translations
     * @return Translations
     */
    private function removeMalformedTranslations(Translations $translations)
    {
        $newTranslations = new Translations();

        /**
         * @var Translation $translation
         */
        foreach ($translations as $i => $translation) {
            if ($translation->isDisabled()) {
                $fixedTranslation = new Translation($translation->getContext(), str_replace(PHP_EOL, '', $translation->getOriginal()));
                $fixedTranslation->setTranslation(str_replace(PHP_EOL, '', $translation->getTranslation()));
                $fixedTranslation->setDisabled(true);
                $newTranslations->append($fixedTranslation);
            } else {
                $newTranslations->append($translation);
            }
        }

        return $newTranslations;
    }
}