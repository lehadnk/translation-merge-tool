<?php

namespace TranslationMergeTool\DTO;


use TranslationMergeTool\Config\Component;

class TranslationFile
{
    /**
     * @var string
     */
    public $relativePath;

    /**
     * @var string
     */
    public $absolutePath;

    /**
     * @var string
     */
    public $weblateCode;

    /**
     * @var bool
     */
    public $isNew;

    /**
     * @var Component
     */
    public $component;

    public function getAbsolutePathToMo()
    {
        return preg_replace("/\.po$/", ".mo", $this->absolutePath);
    }
}
