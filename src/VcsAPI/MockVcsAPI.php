<?php


namespace TranslationMergeTool\VcsAPI;


use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use TranslationMergeTool\DTO\TranslationFile;
use TranslationMergeTool\Output\IOutputInterface;

class MockVcsAPI implements IVcsApi
{
    /** @var TranslationFile[] */
    private $translationFiles = [];
    private IOutputInterface $outputInterface;

    public function __construct(IOutputInterface $outputInterface)
    {
        $this->outputInterface = $outputInterface;
    }

    public function addFile(TranslationFile $translationFile)
    {
        $this->translationFiles[] = $translationFile;
    }

    public function commit(): ResponseInterface
    {
        foreach ($this->translationFiles as $file) {
            $this->outputInterface->debug("Commiting {$file->relativePath}...");
        }
        return new Response(200);
    }

    public function getProviderName(): string
    {
        return "Mock";
    }
}
