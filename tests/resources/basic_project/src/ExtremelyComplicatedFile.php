<?php

namespace resources\project\src;


class ExtremelyComplicatedFile
{
    function test()
    {
        $a = __("Test string");
        $b = __("Это строка с кириллицей");
        return $a + $b + __("Another test string");
    }
}
