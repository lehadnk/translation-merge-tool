<?php
/**
 * Created by PhpStorm.
 * User: lehadnk
 * Date: 2018-11-28
 * Time: 17:24
 */

namespace TranslationMergeTool\CodeParser;


use TranslationMergeTool\Config\Component;

class FileLister
{
    /**
     * @var string[]
     */
    private $gitIgnorePaths;

    /**
     * @param Component $component
     * @param string $workingDir
     * @return string[]
     */
    public function getFileList(Component $component, string $workingDir): array
    {
        $reader = new GitignoreReader();
        $this->gitIgnorePaths = $reader->getIgnoredPaths($workingDir);

        $result = [];
        foreach($component->includeDirectories as $path) {
            $directoryList = $this->getFilesInPath($workingDir.'/'.$path);

            /**
             * @todo Do something with marshaller - it parses empty json array as null value
             */
            $excludeDirectories = $component->excludeDirectories ?? [];

            $filteredList = $this->filterFileList($directoryList, $excludeDirectories, $workingDir);
            $result = array_merge($result, $filteredList);
        }

        return array_unique($result);
    }

    /**
     * @param string $path
     * @return string[]
     */
    private function getFilesInPath(string $path): array
    {
        $directory = new \RecursiveDirectoryIterator($path);
        $iterator = new \RecursiveIteratorIterator($directory);
        $fileList = new \RegexIterator($iterator, '/^.+\.(?:php|js|vue)$/i', \RegexIterator::GET_MATCH);

        $list = [];
        foreach ($fileList as $file) {
            $list[] = $file[0];
        }

        return $list;
    }

    /**
     * @param array $fileList
     * @param array $excludeDirectories
     * @param string $workingDir
     * @return string[]
     */
    private function filterFileList(array $fileList, array $excludeDirectories, string $workingDir): array
    {
        $filteredList = [];
        foreach ($fileList as $path) {
            $excluded = false;

            foreach ($excludeDirectories as $excludeDirectory) {
                $searchSubstr = $workingDir . '/' . $excludeDirectory;
                if (substr($path, 0, strlen($searchSubstr)) === $searchSubstr) $excluded = true;
            }

            foreach ($this->gitIgnorePaths as $excludeDirectory) {
                $searchSubstr = $workingDir . '/' . $excludeDirectory;
                if (substr($path, 0, strlen($searchSubstr)) === $searchSubstr) $excluded = true;
            }

            if (!$excluded) {
                $filteredList[] = $path;
            }
        }

        return $filteredList;
    }
}