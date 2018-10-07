<?php
/**
 * Created by PhpStorm.
 * User: lehadnk
 * Date: 10/7/18
 * Time: 10:53 PM
 */

namespace TranslationMergeTool;


use Gettext\Translations;

class GettextReader
{
    private $translations;

    public function __construct(string $fileName)
    {
        $this->translations = Translations::fromPoFile($fileName);
    }

    public function addNewTranslations(array $strings)
    {
        $originalCount = $this->translations->count();

        foreach ($strings as $string) {
            if (!$this->translations->find($string)) {
                $this->translations->insert('', $string);
            }
        }

        return $this->translations->count() - $originalCount;
    }
}