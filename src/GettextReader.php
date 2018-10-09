<?php
/**
 * Created by PhpStorm.
 * User: lehadnk
 * Date: 10/7/18
 * Time: 10:53 PM
 */

namespace TranslationMergeTool;


use Gettext\Translation;
use Gettext\Translations;
use TranslationMergeTool\DTO\TranslationString;

class GettextReader
{
    private $translations;

    public function __construct(string $fileName)
    {
        $this->translations = Translations::fromPoFile($fileName);
    }

    /**
     * @param TranslationString[] $translationStrings
     * @param string $fileName
     * @return int
     */
    public function addNewTranslations(array $translationStrings, string $fileName)
    {
        $originalCount = $this->translations->count();
        foreach ($translationStrings as $translationString) {
            if (!$this->translations->offsetGet(Translation::generateId('', $translationString->originalString))) {
                $translation = new Translation('', $translationString->originalString);
                $translation->addComment('Branch: '.$translationString->branchName);
                foreach ($translationString->fileReferences as $reference) {
                    $translation->addReference($reference);
                }
                $this->translations->offsetSet(null, $translation);
            }
        }

        $this->translations->toPoFile($fileName);

        return $this->translations->count() - $originalCount;
    }
}