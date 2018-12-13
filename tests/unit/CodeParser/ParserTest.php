<?php
/**
 * Created by PhpStorm.
 * User: lehadnk
 * Date: 27/11/2018
 * Time: 18:45
 */

namespace unit\CodeParser;

use TranslationMergeTool\CodeParser\Parser;
use UnitTests\AbstractTestProjectCase;

class ParserTest extends AbstractTestProjectCase
{
    /**
     * @var Parser
     */
    private $parser;

    public function setUp()
    {
        parent::setUp();
        $this->parser = new Parser($this->getTestComponent(), $this->getTestProjectDir(), 'test');
    }

    public function testGetStrings()
    {
        $strings = $this->parser->getStrings();
        $this->assertArrayHasKey('This translation is included in the project', $strings);
        $this->assertArrayNotHasKey('This translation is excluded from the project', $strings);
        $this->assertArrayNotHasKey('This string is excluded from git', $strings);
        $this->assertArrayHasKey('Smart tools module:', $strings);
        $this->assertArrayHasKey('Available tools:', $strings);
    }

    public function testParseFile()
    {
        $strings = $this->parser->parseFile($this->getTestProjectDir().'/src/ExtremelyComplicatedFile.php');
        $this->assertContains('Test string', $strings);
        $this->assertContains('Another test string', $strings);
        $this->assertNotContains('Это строка с кириллицей', $strings);
    }
}
