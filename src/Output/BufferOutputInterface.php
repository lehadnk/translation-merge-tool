<?php

namespace TranslationMergeTool\Output;

class BufferOutputInterface implements IOutputInterface
{
    private string $buffer = "";

    private function addMessage(string $message)
    {
        if ($this->buffer === "") {
            $this->buffer = $message;
            return;
        }

        $this->buffer .= PHP_EOL . $message;
    }

    public function info(string $msg)
    {
        $this->addMessage($msg);
    }

    public function debug(string $msg)
    {
        $this->addMessage($msg);
    }

    public function error(string $msg)
    {
        $this->addMessage($msg);
    }

    public function critical(string $msg)
    {
        $this->addMessage($msg);
    }

    public function warning(string $msg)
    {
        $this->addMessage($msg);
    }

    public function success(string $msg)
    {
        $this->addMessage($msg);
    }

    public function getBuffer()
    {
        return $this->buffer;
    }
}
