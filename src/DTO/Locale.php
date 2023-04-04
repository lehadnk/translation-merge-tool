<?php
/**
 * Created by PhpStorm.
 * User: lehadnk
 * Date: 31/10/2018
 * Time: 22:17
 */

namespace TranslationMergeTool\DTO;



class Locale
{
    public $localeName;

    public $weblateCode;

    public function __construct(string $localeName)
    {
        $this->localeName = $localeName;
        $this->weblateCode = $this->toWeblateCode($localeName);
    }

    private function toWeblateCode(string $localeName): string
    {
        if ($localeName === 'zh_CN') return 'zh_Hans';
        if ($localeName === 'en_US') return 'en_US';
        if ($localeName === 'en_GB') return 'en_GB';

        return explode('_', $localeName)[0];
    }
}
