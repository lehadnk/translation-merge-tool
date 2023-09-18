<?php

namespace UnitTests\CodeParser;

use TranslationMergeTool\CodeParser\FileLister;
use UnitTests\AbstractBasicCase;

class FileListerTest extends AbstractBasicCase
{
    public function testGetFileList()
    {
        $fileLister = new FileLister();
        $fileList = $fileLister->getFileList($this->getTestComponent(), $this->getTestProjectDir());

        $this->assertContains($this->getFullPath('src/includedDirectory/IncludedFile.php'), $fileList);
        $this->assertContains($this->getFullPath('public/SomeFileOutsideOfIncludeDir.php'), $fileList);
        $this->assertNotContains($this->getFullPath('src/excludedDirectory/ExcludedFile.php'), $fileList);
        $this->assertNotContains($this->getFullPath('src/excludedFromGit/ExcludedFromGit.php'), $fileList);
        $this->assertNotContains($this->getFullPath('src/excludedWithTrailingSlash/ExcludedFromGit.php'), $fileList);
    }

    public function getFullPath(string $dir): string
    {
        return $this->getTestProjectDir().'/'.$dir;
    }
}
