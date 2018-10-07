<?php
/**
 * Created by PhpStorm.
 * User: lehadnk
 * Date: 10/6/18
 * Time: 6:01 PM
 */

require __DIR__.'/vendor/autoload.php';

$workingDir = getcwd();
$app = new \TranslationMergeTool\App($workingDir);
$app->run();