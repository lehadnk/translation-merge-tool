<?php

namespace TranslationMergeTool\DTO;

class CodeParseResult
{
    public readonly int $newStrings;
    /** @var TranslationFile[]  */
    public readonly array $affectedTranslationFiles;

    public function __construct(
        int $newStrings,
        array $affectedTranslationFiles
    ) {
        $this->newStrings = $newStrings;
        $this->affectedTranslationFiles = $affectedTranslationFiles;
    }
}
