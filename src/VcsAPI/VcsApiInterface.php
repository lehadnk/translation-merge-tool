<?php
/**
 * Created by PhpStorm.
 * User: lehadnk
 * Date: 30/10/2018
 * Time: 20:01
 */

namespace TranslationMergeTool\VcsAPI;


use Psr\Http\Message\ResponseInterface;

interface VcsApiInterface
{
    public function addFile(string $fileName, string $remoteFileName);
    public function commit(): ResponseInterface;
}