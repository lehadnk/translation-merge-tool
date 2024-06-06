<?php

namespace UnitTests\Application;

use TranslationMergeTool\Application\Application;
use TranslationMergeTool\DTO\Arguments;
use TranslationMergeTool\Environment\EnvironmentFactory;
use TranslationMergeTool\Output\BufferOutputInterface;
use TranslationMergeTool\PoReader\GettextReader;
use TranslationMergeTool\System\Git;
use TranslationMergeTool\WeblateAPI\MockWeblateAPI;
use UnitTests\AbstractMonorepCase;

class ApplicationTest extends AbstractMonorepCase
{
    public function testVersion()
    {
        $bufferedOI = new BufferOutputInterface();
        $application = $this->getApplication($bufferedOI, new Arguments(
            false, false, false, false, false, true, false, false, false
        ));

        $exitCode = $application->run();

        $this->assertEquals(0, $exitCode);
        $this->assertEquals("1.6.3", $bufferedOI->getBuffer());
    }

    public function testWeblatePull()
    {
        $bufferedOI = new BufferOutputInterface();
        $application = $this->getApplication($bufferedOI, new Arguments(
            true, false, false, false, false, false, false, true, false
        ));

        $exitCode = $application->run();

        $this->assertEquals(0, $exitCode);
        $this->assertEquals("Pulling the Weblate component monorep/api...\nPulling weblate component...\nPulling the Weblate component monorep/databroker...\nPulling weblate component...", $bufferedOI->getBuffer());
    }

    public function testJustParse()
    {
        $bufferedOI = new BufferOutputInterface();
        $application = $this->getApplication($bufferedOI, new Arguments(
            false, true, false, false, false, false, false, true, false
        ));

        $exitCode = $application->run();
        $this->assertEquals(0, $exitCode);

        $outputDatabrokerGettextFile = GettextReader::readFile($this->getTestProjectDir() . '/translations/gb/databroker.po');
        $this->assertEquals(1, $outputDatabrokerGettextFile->translations->count());

        $outputDatabrokerGettextFile = GettextReader::readFile($this->getTestProjectDir() . '/translations/gb/api.po');
        $this->assertEquals(1, $outputDatabrokerGettextFile->translations->count());
    }

    public function testCheck()
    {
        $bufferedOI = new BufferOutputInterface();
        $application = $this->getApplication($bufferedOI, new Arguments(
            false, false, true, false, true, false, false, true, false
        ));

        $exitCode = $application->run();
        $this->assertEquals(0, $exitCode);

        $this->assertStringContainsString('Translation key from api application', $bufferedOI->getBuffer());
        $this->assertStringNotContainsString('Translation key from databroker application', $bufferedOI->getBuffer());
    }

    public function testPrune()
    {
        $bufferedOI = new BufferOutputInterface();
        $application = $this->getApplication($bufferedOI, new Arguments(
            false, false, false, true, false, false, false, true, true
        ));

        MockWeblateAPI::$downloadTranslationFileContentsLambda = function(string $projectSlug, string $componentSlug, string $localeName) {
            if ($localeName == 'gb') {
                return '';
            }
            return file_get_contents($this->getTestProjectDir() . 'translations/' . $localeName . '/' . $componentSlug . '.po');
        };

        $exitCode = $application->run();
        $this->assertEquals(0, $exitCode);

        $newPoFileContents = file_get_contents($this->getTestProjectDir() . '/translations/fr/databroker.po');
        $this->assertStringContainsString('#~ msgid "Translation key from api application"', $newPoFileContents);
        $this->assertStringNotContainsString('#~ msgid "Value cannot be null"', $newPoFileContents);
    }

    public function testRun()
    {
        $bufferedOI = new BufferOutputInterface();
        $application = $this->getApplication($bufferedOI, new Arguments(
            false, false, false, false, false, false, false, true, true
        ));

        MockWeblateAPI::$downloadTranslationFileContentsLambda = function(string $projectSlug, string $componentSlug, string $localeName) {
            if ($localeName == 'gb') {
                return '';
            }
            return file_get_contents($this->getTestProjectDir() . 'translations/' . $localeName . '/' . $componentSlug . '.po');
        };

        $exitCode = $application->run();
        $this->assertEquals(0, $exitCode);
    }

    private function getApplication(BufferOutputInterface $bufferedOI,Arguments $arguments)
    {
        $application = new Application(
            $this->getTestProjectDir(),
            $arguments,
            (new EnvironmentFactory())->build(),
            $bufferedOI,
            new Git("master")
        );

        return $application;
    }
}
