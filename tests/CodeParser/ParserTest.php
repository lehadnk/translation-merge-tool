<?php
/**
 * Created by PhpStorm.
 * User: lehadnk
 * Date: 27/11/2018
 * Time: 18:45
 */

namespace CodeParser;

use PHPUnit\Framework\TestCase;
use TranslationMergeTool\CodeParser\Parser;
use TranslationMergeTool\Config\Component;
use TranslationMergeTool\Config\ConfigFactory;

class ParserTest extends TestCase
{
    private $config;

    /**
     * @var Parser
     */
    private $parser;

    public function setUp()
    {
        $config = ConfigFactory::read($this->getTestProjectDir().'/.translate-config.json');
        $component = $config->components[0];
        $this->parser = new Parser($component, $this->getTestProjectDir(), 'test');
    }

    private function getTestProjectDir()
    {
        return __DIR__.'/../project';
    }

    public function testGetStrings()
    {
        $strings = $this->parser->getStrings();
        $this->assertTrue(array_key_exists('This translation is included in the project', $strings));
        $this->assertFalse(array_key_exists('This translation is excluded from the project', $strings));
    }

    public function testParseFile()
    {
        $strings = $this->parser->parseFile($this->getTestProjectDir().'/src/ExtremelyComplicatedFile.php');
        $this->assertTrue(in_array('Test string', $strings));
        $this->assertTrue(in_array('Another test string', $strings));
        $this->assertFalse(in_array('Это строка с кириллицей', $strings));
    }
}
