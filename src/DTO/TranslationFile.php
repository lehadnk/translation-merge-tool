<?php
/**
 * Created by PhpStorm.
 * User: lehadnk
 * Date: 10/7/18
 * Time: 11:10 PM
 */

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