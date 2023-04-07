<?php
/**
 * Created by PhpStorm.
 * User: lehadnk
 * Date: 2018-12-05
 * Time: 16:43
 */

namespace UnitTests\Config\ComposerJson;

use TranslationMergeTool\Config\ComposerJson\ComposerJsonFactory;
use UnitTests\AbstractCase;

class ComposerJsonTest extends AbstractCase
{

    public function testAddNewTranslations()
    {
        $composerJsonFactory = new ComposerJsonFactory();
        $composerJson = $composerJsonFactory->read();

        $this->assertNotNull($composerJson->version);
    }
}
