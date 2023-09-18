<?php


namespace TranslationMergeTool\WeblateAPI;


use TranslationMergeTool\Output\IOutputInterface;

class MockWeblateAPI implements IWeblateAPI
{
    private IOutputInterface $outputInterface;
    public static $downloadTranslationFileContentsLambda;

    public function __construct(IOutputInterface $outputInterface)
    {
        $this->outputInterface = $outputInterface;
    }

    public function commitComponent(string $projectSlug, string $componentSlug)
    {
        $this->outputInterface->debug("Commiting weblate component...");
    }

    public function pushComponent(string $projectSlug, string $componentSlug)
    {
        $this->outputInterface->debug("Pushing weblate component...");
    }

    public function pullComponent(string $projectSlug, string $componentSlug)
    {
        $this->outputInterface->debug("Pulling weblate component...");
    }

    public function downloadTranslationFile(string $projectSlug, string $componentSlug, string $localeName): string
    {
        $this->outputInterface->debug("Downloading translation file...");
        return call_user_func(self::$downloadTranslationFileContentsLambda, $projectSlug, $componentSlug, $localeName);
    }
}
