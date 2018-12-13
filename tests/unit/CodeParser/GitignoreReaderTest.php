<?php
/**
 * Created by PhpStorm.
 * User: lehadnk
 * Date: 2018-11-28
 * Time: 18:05
 */

namespace UnitTests\CodeParser;

use TranslationMergeTool\CodeParser\GitignoreReader;
use UnitTests\AbstractTestProjectCase;

class GitignoreReaderTest extends AbstractTestProjectCase
{

    public function testGetIgnoredPaths()
    {
        $reader = new GitignoreReader();
        $paths = $reader->getIgnoredPaths($this->getTestProjectDir());

        $this->assertContains('src/excludedFromGit', $paths);
        $this->assertNotContains('', $paths);
    }
}
