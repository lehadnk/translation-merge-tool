<?php
/**
 * Created by PhpStorm.
 * User: lehadnk
 * Date: 2018-11-28
 * Time: 18:02
 */

namespace TranslationMergeTool\CodeParser;


class GitignoreReader
{
    /**
     * @param string $workingDir
     * @return string[]
     */
    public function getIgnoredPaths(string $workingDir): array
    {
        if (!file_exists($workingDir.'/.gitignore')) {
            return [];
        }

        $contents = file_get_contents($workingDir.'/.gitignore');
        return explode(PHP_EOL, $contents);
    }
}