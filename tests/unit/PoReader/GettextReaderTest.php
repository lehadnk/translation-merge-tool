<?php

namespace UnitTests\PoReader;

use TranslationMergeTool\DTO\TranslationString;
use TranslationMergeTool\PoReader\GettextReader;
use UnitTests\AbstractBasicCase;

class GettextReaderTest extends AbstractBasicCase
{
    public function testAddNewTranslations()
    {
        $reader = GettextReader::readFile($this->getTestProjectDir().'/translations/ru/translation.po');

        $addedStrings = [
            new TranslationString('This string exists in .po file', ['testfile'], 'test'),
            new TranslationString('Available tools:', ['testfile'], 'test'),
            new TranslationString('This string does not exist in .po file', ['testfile'], 'test')
        ];
        $newStrings = $reader->addNewTranslations($addedStrings);

        $this->assertNotContains('This string exists in .po file', $newStrings);
        $this->assertContains('Available tools:', $newStrings);
        $this->assertContains('This string does not exist in .po file', $newStrings);
    }
}
