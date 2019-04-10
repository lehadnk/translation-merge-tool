<?php
/**
 * Created by PhpStorm.
 * User: lehadnk
 * Date: 2018-11-28
 * Time: 17:35
 */

namespace UnitTests;

use TranslationMergeTool\Config\Component;
use TranslationMergeTool\Config\Config;
use TranslationMergeTool\Config\ConfigFactory;
use PHPUnit\Framework\TestCase;

abstract class AbstractTestProjectCase extends TestCase
{
    /**
     * @var Config
     */
    protected $config;

    public function setUp()
    {
        $this->config = ConfigFactory::read($this->getTestProjectDir().'/.translate-config.json');
    }

    protected function getTestProjectDir()
    {
        return __DIR__.'/../project';
    }

    protected function getTestComponent(): Component
    {
        return $this->config->components[0];
    }
}
