<?php
/**
 * Created by PhpStorm.
 * User: lehadnk
 * Date: 27/11/2018
 * Time: 19:09
 */

namespace project\src;


class ExtremelyComplicatedFile
{
    function test()
    {
        $a = __("Test string");
        $b = __("Это строка с кириллицей");
        return $a + $b + __("Another test string");
    }
}