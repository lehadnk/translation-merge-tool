<?php

namespace TranslationMergeTool\Output;

interface IOutputInterface
{
    public function info(string $msg);
    public function debug(string $msg);
    public function error(string $msg);
    public function critical(string $msg);
    public function warning(string $msg);
    public function success(string $msg);
}
