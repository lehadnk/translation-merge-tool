<?php
/**
 * Created by PhpStorm.
 * User: lehadnk
 * Date: 30/10/2018
 * Time: 20:01
 */

namespace TranslationMergeTool\VcsAPI;


use Psr\Http\Message\ResponseInterface;
use TranslationMergeTool\DTO\TranslationFile;

interface VcsApiInterface
{
    public function addFile(TranslationFile $translationFile);
    public function commit(): ResponseInterface;
}