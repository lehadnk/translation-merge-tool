<?php

namespace TranslationMergeTool\DTO;


class TranslationString
{
    public function __construct(string $originalString, array $fileReferences, string $branchName)
    {
        $this->originalString = $originalString;
        $this->fileReferences = $fileReferences;
        $this->branchName = $branchName;
    }

    /**
     * @var string
     */
    public $originalString;

    /**
     * @var string[]
     */
    public $fileReferences;

    /**
     * @var string
     */
    public $branchName;
}
