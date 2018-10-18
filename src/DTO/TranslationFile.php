<?php
/**
 * Created by PhpStorm.
 * User: lehadnk
 * Date: 10/7/18
 * Time: 11:10 PM
 */

namespace TranslationMergeTool\DTO;


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

    public function getAbsolutePathToMo()
    {
        return preg_replace("/\.po$/", ".mo", $this->absolutePath);
    }
}