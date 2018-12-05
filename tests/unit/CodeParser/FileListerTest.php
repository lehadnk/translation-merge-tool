<?php
/**
 * Created by PhpStorm.
 * User: lehadnk
 * Date: 2018-11-28
 * Time: 17:34
 */

namespace CodeParser;

use PHPUnit\Framework\TestCase;
use SebastianBergmann\CodeCoverage\Node\File;
use TranslationMergeTool\CodeParser\FileLister;
use UnitTests\AbstractTestProjectCase;

class FileListerTest extends AbstractTestProjectCase
{
    public function testGetFileList()
    {
        $fileLister = new FileLister();
        $fileList = $fileLister->getFileList($this->getTestComponent(), $this->getTestProjectDir());

        $this->assertTrue(in_array($this->getFullPath('src/includedDirectory/IncludedFile.php'), $fileList));
        $this->assertFalse(in_array($this->getFullPath('src/excludedDirectory/ExcludedFile.php'), $fileList));
        $this->assertFalse(in_array($this->getFullPath('src/excludedFromGit/ExcludedFromGit.php'), $fileList));
        $this->assertFalse(in_array($this->getFullPath('src/excludedWithTrailingSlash/ExcludedFromGit.php'), $fileList));
    }

    public function getFullPath(string $dir): string
    {
        return $this->getTestProjectDir().'/'.$dir;
    }
}
