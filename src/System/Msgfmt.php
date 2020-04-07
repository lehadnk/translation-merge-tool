<?php


namespace TranslationMergeTool\System;


class Msgfmt implements ExternalApplication
{
    public function isInstalled(): bool
    {
        return Terminal::run('msgfmt')->code !== 127;
    }
}