<?php

namespace TranslationMergeTool\System;

interface ExternalApplication
{
    public function isInstalled(): bool;
}