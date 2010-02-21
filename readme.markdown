PHPLint Wrapper Library
=======================

This library provides a simple means to run a lint (aka syntactical) check on php code
from within a php application:

Usage with file
---------------

$linter = new TheSeer\Tools\PHPLint();
$x = $linter->lintFile(__FILE__);

if (!$x) {
   var_dump($x, $linter->getError());<?php
}


Usage with string
-----------------

$linter = new TheSeer\Tools\PHPLint();
$x = $linter->lintString('<?php dddd');

var_dump($x, $linter->getError());

