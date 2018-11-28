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
        $this->assertTrue(array_key_exists('This translation is included in the project', $strings));
        $this->assertFalse(array_key_exists('This translation is excluded from the project', $strings));
        $this->assertFalse(array_key_exists('This string is excluded from git', $strings));
    }

    public function testParseFile()
    {
        $strings = $this->parser->parseFile($this->getTestProjectDir().'/src/ExtremelyComplicatedFile.php');
        $this->assertTrue(in_array('Test string', $strings));
        $this->assertTrue(in_array('Another test string', $strings));
        $this->assertFalse(in_array('Это строка с кириллицей', $strings));
    }
}
