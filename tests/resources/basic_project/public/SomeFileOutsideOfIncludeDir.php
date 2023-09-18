<?php

namespace resources\project\public;
class SomeFileOutsideOfIncludeDir
{
    public function test()
    {
        return __("Test string outside of include dir");
    }
}
