<?php

require __DIR__ . '/../src/phplint.php';

$linter = new TheSeer\Tools\PHPLint();
$x = $linter->lintFile(__FILE__);

var_dump($x, $linter->getError());