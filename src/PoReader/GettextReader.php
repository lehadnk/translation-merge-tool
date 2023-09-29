<?php

namespace TranslationMergeTool\PoReader;


use Gettext\Translation;
use Gettext\Translations;
use TranslationMergeTool\DTO\TranslationString;

class GettextReader
{
    /**
     * @var Translation[]
     */
    public $translations;

    /**
     * @var string
     */
    private $fileName;

    public function __construct(Translations $translations, string $fileName)
    {
        $this->translations = $translations;
        $this->fileName = $fileName;
    }

    /**
     * @param string $filename
     * @return GettextReader
     */
    public static function readFile(string $filename)
    {
        return new self(Translations::fromPoFile($filename), $filename);
    }

    /**
     * @param string $filename
     * @return GettextReader
     */
    public static function newFile(string $filename)
    {
        return new self (new Translations(), $filename);
    }

    /**
     * @param TranslationString[] $translationStrings
     * @return string[] Added strings
     */
    public function addNewTranslations(array $translationStrings)
    {
        $addedStrings = [];
        foreach ($translationStrings as $translationString) {
            $id = Translation::generateId('', $translationString->originalString);

            if (isset($this->translations[$id])) {
                if ($this->translations[$id]->isDisabled()) {
                    $this->translations[$id]->setDisabled(false);
                    $addedStrings[] = $translationString->originalString;
                    continue;
                } else {
                    continue;
                }
            } else {
                $addedStrings[] = $translationString->originalString;
            }

            $translation = new Translation('', $translationString->originalString);
            $translation->addComment('Branch: '.$translationString->branchName);
            foreach ($translationString->fileReferences as $reference) {
                $translation->addReference($reference);
            }
            $this->translations->offsetSet(null, $translation);
        }

        return $addedStrings;
    }

    public function save()
    {
        $this->translations->toPoFile($this->fileName);
    }

    private function getBranchNameByTranslation(Translation $translation)
    {
        foreach ($translation->getComments() as $comment) {
            if (strpos($comment, "Branch: ") === 0) {
                $branchName = str_replace("Branch: ", "", $comment);
                return trim(mb_strtolower($branchName));
            }
        }
        return null;
    }

    public function getUntranslatedStringsAddedInBranch($branchName)
    {
        $result = [];
        $branchName = trim(mb_strtolower($branchName));
        foreach ($this->translations as $translation) {
            if ($translation->isDisabled() || $translation->getTranslation()) {
                continue;
            }
            $translationAddedInBranch = $this->getBranchNameByTranslation($translation);
            if ($translationAddedInBranch === $branchName) {
                $result[] = $translation->getOriginal();
            }
        }

        return $result;
    }
}
