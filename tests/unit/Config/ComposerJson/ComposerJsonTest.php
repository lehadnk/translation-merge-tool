<?php

namespace UnitTests\Config\ComposerJson;

use TranslationMergeTool\Config\ComposerJson\ComposerJsonFactory;
use UnitTests\AbstractBasicCase;
use UnitTests\AbstractCase;

class ComposerJsonTest extends AbstractBasicCase
{
    public function testReadingComposerJson()
    {
        $composerJsonFactory = new ComposerJsonFactory();
        $composerJson = $composerJsonFactory->read();

        $this->assertNotNull($composerJson->version);
    }
}
