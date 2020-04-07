<?php


namespace TranslationMergeTool\System;


class Git implements ExternalApplication
{
    public function isInstalled(): bool
    {
        return Terminal::run('git')->code !== 127;
    }
}