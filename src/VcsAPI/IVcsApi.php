<?php

namespace TranslationMergeTool\VcsAPI;


use Psr\Http\Message\ResponseInterface;
use TranslationMergeTool\DTO\TranslationFile;

interface IVcsApi
{
    public function addFile(TranslationFile $translationFile);
    public function commit(): ResponseInterface;
    public function getProviderName(): string;
}
