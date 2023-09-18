<?php

namespace TranslationMergeTool\Output;

use splitbrain\phpcli\CLI;

class CliOutputInterface implements IOutputInterface
{
    private CLI $cli;

    public function __construct(CLI $cli)
    {
        $this->cli = $cli;
    }

    public function info(string $msg)
    {
        $this->cli->info($msg);
    }

    public function debug(string $msg)
    {
        $this->cli->debug($msg);
    }

    public function error(string $msg)
    {
        $this->cli->error($msg);
    }

    public function critical(string $msg)
    {
        $this->cli->critical($msg);
    }

    public function warning(string $msg)
    {
        $this->cli->warning($msg);
    }

    public function success(string $msg)
    {
        $this->cli->success($msg);
    }
}
