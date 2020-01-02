<?php
/**
 * Created by PhpStorm.
 * User: lehadnk
 * Date: 2018-12-05
 * Time: 16:43
 */

namespace UnitTests\PoReader;

use TranslationMergeTool\DTO\TranslationString;
use TranslationMergeTool\PoReader\GettextReader;
use UnitTests\AbstractCase;

class GettextReaderTest extends AbstractCase
{

    public function testAddNewTranslations()
    {
        $reader = GettextReader::readFile($this->getTestProjectDir().'/translations/ru/translation.po');

        $addedStrings = [
            new TranslationString('This string exists in .po file', ['testfile'], 'test'),
            new TranslationString('Available tools:', ['testfile'], 'test'),
            new TranslationString('This string is not existing in .po file', ['testfile'], 'test')
        ];
        $newStrings = $reader->addNewTranslations($addedStrings);

        $this->assertNotContains('This string exists in .po file', $newStrings);
        $this->assertContains('Available tools:', $newStrings);
        $this->assertContains('This string is not existing in .po file', $newStrings);
    }
}
