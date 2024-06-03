<?php

namespace UnitTests\CodeParser;

use TranslationMergeTool\CodeParser\ComponentParser;
use UnitTests\AbstractBasicCase;

class ParserTest extends AbstractBasicCase
{
    private ComponentParser $parser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->parser = new ComponentParser($this->getTestComponent(), $this->getTestProjectDir(), 'test');
    }

    public function testGetStrings()
    {
        $strings = $this->parser->getStrings();
        $this->assertArrayHasKey('This translation is included in the project', $strings);
        $this->assertArrayNotHasKey('This translation is excluded from the project', $strings);
        $this->assertArrayNotHasKey('This string is excluded from git', $strings);
        $this->assertArrayHasKey('Smart tools module:', $strings);
        $this->assertArrayHasKey('Available tools:', $strings);
        $this->assertArrayHasKey('my translated text', $strings);
        $this->assertArrayHasKey('After you press the "Create campaign" button the campaign will be created and sent to the moderation. You can change it anytime', $strings);
        $this->assertArrayHasKey('Test %placeholder%', $strings);
    }

    public function testParseFile()
    {
        $strings = $this->parser->parseFile($this->getTestProjectDir().'/src/ExtremelyComplicatedFile.php');
        $this->assertContains('Test string', $strings);
        $this->assertContains('Another test string', $strings);
        $this->assertNotContains('Это строка с кириллицей', $strings);
    }

    public function testParseJavaAnnotation()
    {
        $strings = $this->parser->parseFile($this->getTestProjectDir().'/src/SpringValidationMessage.java');
        var_dump($strings);
        $this->assertContains('User email cannot be blank', $strings);
        $this->assertContains('Password cannot be blank', $strings);
        $this->assertEquals(count($strings), 2);
    }
}
