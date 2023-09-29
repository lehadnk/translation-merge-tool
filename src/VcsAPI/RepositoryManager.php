<?php

namespace TranslationMergeTool\VcsAPI;

use TranslationMergeTool\Output\IOutputInterface;

class RepositoryManager
{
    private IOutputInterface $outputInterface;
    private IVcsApi $vcsApi;

    public function __construct(
        IOutputInterface $outputInterface,
        IVcsApi $vcsApi
    ) {
        $this->outputInterface = $outputInterface;
        $this->vcsApi = $vcsApi;
    }

    public function pushToVcs(array $affectedTranslationFiles)
    {
        $this->outputInterface->info("Pushing updated files to {$this->vcsApi->getProviderName()}...");

        if (count($affectedTranslationFiles) === 0) {
            $this->outputInterface->info("No translation files are updated, skipping pushing to VCS");
            return;
        }

        foreach ($affectedTranslationFiles as $translationFile) {
            $this->outputInterface->debug("Adding {$translationFile->relativePath} to the commit...");
            $this->vcsApi->addFile($translationFile);
        }

        $result = $this->vcsApi->commit();

        if ($result->getStatusCode() === 2) {
            $this->outputInterface->error("Unable to push {$translationFile->relativePath} to the repository!");
            $this->outputInterface->debug($result->getStatusCode());
            $this->outputInterface->debug($result->getReasonPhrase());
            exit(2);
        }
    }
}
