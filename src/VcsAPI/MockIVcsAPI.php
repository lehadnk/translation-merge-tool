<?php


namespace TranslationMergeTool\VcsAPI;


use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use TranslationMergeTool\Console;
use TranslationMergeTool\DTO\TranslationFile;

class MockIVcsAPI implements IVcsApi
{
    /**
     * @var TranslationFile[]
     */
    private $translationFiles = [];

    public function addFile(TranslationFile $translationFile)
    {
        $this->translationFiles[] = $translationFile;
    }

    public function commit(): ResponseInterface
    {
        foreach ($this->translationFiles as $file) {
            Console::debug("Commiting {$file->relativePath}...");
        }
        return new Response(200);
    }
}