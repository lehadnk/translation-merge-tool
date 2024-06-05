<?php

namespace TranslationMergeTool\DTO;

class Arguments
{
    public readonly bool $weblatePull;
    public readonly bool $justParse;
    public readonly bool $check;
    public readonly bool $prune;
    public readonly bool $printUntranslated;
    public readonly bool $version;
    public readonly bool $force;
    public readonly bool $noWeblate;
    public readonly bool $autoconfirm;

    public function __construct(
        bool $weblatePull,
        bool $justParse,
        bool $check,
        bool $prune,
        bool $printUntranslated,
        bool $version,
        bool $force,
        bool $noWeblate,
        bool $autoconfirm
    ) {
        $this->weblatePull = $weblatePull;
        $this->justParse = $justParse;
        $this->check = $check;
        $this->prune = $prune;
        $this->printUntranslated = $printUntranslated;
        $this->version = $version;
        $this->force = $force;
        $this->noWeblate = $noWeblate;
        $this->autoconfirm = $autoconfirm;
    }
}
