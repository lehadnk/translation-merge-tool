<?php


namespace TranslationMergeTool\System;


class Git implements ExternalApplication
{
    private string $currentBranch;

    public function __construct(string $currentBranch)
    {
        $this->currentBranch = $currentBranch;
    }

    public function isInstalled(): bool
    {
        return Shell::run('git')->code !== 127;
    }

    public function getCurrentBranchName(): string
    {
        if ($this->currentBranch == null) {
            $this->currentBranch = trim(`git rev-parse --abbrev-ref HEAD`);
        }

        return $this->currentBranch;
    }
}
