<?php

namespace resources\project\src\includedDirectory;


class IncludedFile
{
    function test()
    {
        $a = __("%s's wallet \"%s\"", '+7 (958) 111-22-33', 'Test-partner');
        return __("This translation is included in the project");
    }
}
