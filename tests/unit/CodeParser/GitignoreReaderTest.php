<?php

namespace UnitTests\CodeParser;

use TranslationMergeTool\CodeParser\GitignoreReader;
use UnitTests\AbstractBasicCase;
use UnitTests\AbstractCase;

class GitignoreReaderTest extends AbstractBasicCase
{
    public function testGetIgnoredPaths()
    {
        $reader = new GitignoreReader();
        $paths = $reader->getIgnoredPaths($this->getTestProjectDir());

        $this->assertContains('src/excludedFromGit', $paths);
        $this->assertNotContains('', $paths);
    }
}
