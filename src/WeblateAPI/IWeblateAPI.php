<?php

namespace TranslationMergeTool\WeblateAPI;

interface IWeblateAPI
{
    public function commitComponent(string $projectSlug, string $componentSlug);

    public function pushComponent(string $projectSlug, string $componentSlug);

    public function pullComponent(string $projectSlug, string $componentSlug);

    public function downloadTranslationFile(string $projectSlug, string $componentSlug, string $localeName): string;
}
