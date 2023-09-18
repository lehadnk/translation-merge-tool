<?php

namespace UnitTests;

abstract class AbstractBasicCase extends AbstractCase
{
    protected function getTestProjectDir()
    {
        return $this->testsTmp . 'basic_project/';
    }
}
