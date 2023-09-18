<?php

namespace UnitTests;

abstract class AbstractMonorepCase extends AbstractCase
{
    protected function getTestProjectDir()
    {
        return $this->testsTmp . 'monorep_project/';
    }
}
