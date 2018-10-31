<?php
/**
 * Created by PhpStorm.
 * User: lehadnk
 * Date: 10/7/18
 * Time: 2:10 PM
 */

namespace TranslationMergeTool\Config;
use PhpJsonMarshaller\Annotations\MarshallProperty;
use TranslationMergeTool\DTO\Locale;

class Component
{
    /**
     * @MarshallProperty(name="name", type="string")
     */
    public $name;

    /**
     * @MarshallProperty(name="includeDirectories", type="string[]")
     */
    public $includeDirectories;

    /**
     * @MarshallProperty(name="excludeDirectories", type="string[]")
     */
    public $excludeDirectories;

    /**
     * @MarshallProperty(name="translationFileName", type="string")
     */
    public $translationFileName;

    public function getTranslationFileName(string $languageCode): string
    {
        return str_replace('{localeName}', $languageCode, $this->translationFileName);
    }

    /**
     * @param string $workingDir
     * @return Locale[]
     */
    public function getLocaleList(string $workingDir): array
    {
        $path = $workingDir.'/'.explode('{localeName}', $this->translationFileName)[0];

        $locales = [];
        $dir = new \DirectoryIterator($path);
        foreach ($dir as $fileInfo) {
            if ($fileInfo->isDir() && !$fileInfo->isDot()) {
                $locales[] = new Locale($fileInfo->getFilename());
            }
        }

        return $locales;
    }
}