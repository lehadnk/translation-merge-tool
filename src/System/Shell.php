<?php


namespace TranslationMergeTool\System;


use TranslationMergeTool\DTO\RunCommandResult;

class Shell
{
    public static function run(string $command): RunCommandResult
    {
        exec($command.' 2>&1', $output, $resultCode);

        $result = new RunCommandResult();
        $result->code = $resultCode;
        $result->outputArr = $output;
        $result->firstOutputString = $output[0] ?? null;

        return $result;
    }
}
