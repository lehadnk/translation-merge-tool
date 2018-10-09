<?php
/**
 * Created by PhpStorm.
 * User: lehadnk
 * Date: 10/9/18
 * Time: 5:49 PM
 */

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