<?php

namespace UnitTests\CodeParser;

use TranslationMergeTool\PoReader\PoPostProcessor;
use UnitTests\AbstractBasicCase;

class PoPostProcessorTest extends AbstractBasicCase
{
    private $malformedString = '#~ msgid ""';

    public function testPostProcessPoFile()
    {
        $contents = file_get_contents($this->getTestProjectDir().'/translations/ru/broken_translation_file.po');
        
        $processor = new PoPostProcessor();
        $fixedFile = $processor->postProcessPoFile($contents);
        
        $this->assertStringContainsString($this->malformedString, $contents);
        $this->assertStringNotContainsString($this->malformedString, $fixedFile);
    }
}
