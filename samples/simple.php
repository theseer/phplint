<?php

require __DIR__ . '/../src/phplint.php';

$linter = new TheSeer\Tools\PHPLint();
$x = $linter->lintString('<?php dddd');

var_dump($x, $linter->getError());