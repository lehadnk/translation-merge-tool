<?php

namespace TranslationMergeTool\WeblateAPI;

use TranslationMergeTool\Config\Config;
use TranslationMergeTool\DTO\TranslationFile;
use TranslationMergeTool\Output\IOutputInterface;
use TranslationMergeTool\PoReader\PoPostProcessor;

class WeblateManager
{
    private IOutputInterface $outputInterface;
    private IWeblateAPI $weblateAPI;
    private Config $config;

    public function __construct(
        IOutputInterface $outputInterface,
        IWeblateAPI $weblateAPI,
        Config $config
    ) {
        $this->outputInterface = $outputInterface;
        $this->weblateAPI = $weblateAPI;
        $this->config = $config;
    }

    public function downloadTranslations(array $translationFiles)
    {
        $totalUpdated = 0;
        $total = count($translationFiles);
        $this->outputInterface->info("Downloading new translation files from Weblate...");
        foreach ($translationFiles as $translationFile) {
            /** @var TranslationFile $translationFile */
            $oldFileHash = file_exists($translationFile->absolutePath) ? md5(file_get_contents($translationFile->absolutePath)) : '';
            $fileContents = $this->weblateAPI->downloadTranslationFile(
                $translationFile->component->weblateProjectSlug,
                $translationFile->component->weblateComponentSlug,
                $translationFile->weblateCode
            );
            $newFileHash = md5($fileContents);

            if ($oldFileHash == $newFileHash) {
                $this->outputInterface->info("No changes for {$translationFile->absolutePath}...");
            } else {
                $this->outputInterface->info("Updating {$translationFile->absolutePath}...");
                $totalUpdated++;
            }

            $processor = new PoPostProcessor();
            $fileContents = $processor->postProcessPoFile($fileContents);
            file_put_contents($translationFile->absolutePath, $fileContents);

            // @todo it's so questionable to have this here
            if ($translationFile->component->compileMo) {
                $moPath = $translationFile->getAbsolutePathToMo();
                exec("msgfmt -o $moPath {$translationFile->absolutePath} > /dev/null 2>&1");
            }

            if ($this->config->outputJson) {
                exec("i18next-conv -l {$translationFile->weblateCode} -s {$translationFile->absolutePath} -t {$translationFile->absolutePath}.json");
            }
        }

        $this->outputInterface->info("Total updated translation files: $totalUpdated / $total");
    }
}
