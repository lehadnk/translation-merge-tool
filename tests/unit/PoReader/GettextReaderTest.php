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
use PHPUnit\Framework\TestCase;
use UnitTests\AbstractTestProjectCase;

class GettextReaderTest extends AbstractTestProjectCase
{

    public function testAddNewTranslations()
    {
        $reader = new GettextReader($this->getTestProjectDir().'/translations/ru/translation.po');

        $addedStrings = [
            new TranslationString('This string exists in .po file', ['testfile'], 'test'),
            new TranslationString('Available tools:', ['testfile'], 'test'),
            new TranslationString('This string is not existing in .po file', ['testfile'], 'test')
        ];
        $newStrings = $reader->addNewTranslations($addedStrings);

        $this->assertFalse(in_array('This string exists in .po file', $newStrings));
        $this->assertTrue(in_array('Available tools:', $newStrings));
        $this->assertTrue(in_array('This string is not existing in .po file', $newStrings));
    }
}
