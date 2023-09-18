<?php

namespace UnitTests\CodeParser;

use TranslationMergeTool\PoReader\PoPostProcessor;
use UnitTests\AbstractBasicCase;
use UnitTests\AbstractCase;

class PoPostProcessorTest extends AbstractBasicCase
{
    private $malformedString = '#~ msgid ""';

    public function testPostProcessPoFile()
    {
        $contents = file_get_contents($this->getTestProjectDir().'/translations/ru/broken_translation_file.po');
        
        $processor = new PoPostProcessor();
        $fixedFile = $processor->postProcessPoFile($contents);
        
        $this->assertContains($this->malformedString, $contents);
        $this->assertNotContains($this->malformedString, $fixedFile);
    }
}
