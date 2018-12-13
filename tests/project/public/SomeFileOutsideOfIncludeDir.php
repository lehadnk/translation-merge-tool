<?php
/**
 * Created by PhpStorm.
 * User: lehadnk
 * Date: 2018-12-13
 * Time: 14:35
 */

class SomeFileOutsideOfIncludeDir
{
    public function test()
    {
        return __("Test string outside of include dir");
    }
}