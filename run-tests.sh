#!/bin/sh
phpunit tests --bootstrap vendor/autoload.php --test-suffix Test.php --coverage-html tests/coverage --whitelist src