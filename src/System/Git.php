<?php


namespace TranslationMergeTool\System;


class Git implements ExternalApplication
{
    public function isInstalled(): bool
    {
        return Shell::run('git')->code !== 127;
    }

    public function getCurrentBranchName(): string
    {
        return trim(`git rev-parse --abbrev-ref HEAD`);
    }
}
