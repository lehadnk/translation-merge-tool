<?php


namespace TranslationMergeTool\System;


class Msgfmt implements ExternalApplication
{
    public function isInstalled(): bool
    {
        return Shell::run('msgfmt')->code !== 127;
    }
}
