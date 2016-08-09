<?php

/** @var Composer\Autoload\ClassLoader $classLoader */
$classLoader = require __DIR__ . '/../vendor/autoload.php';

Symplify\PHP7_CodeSniffer\Legacy\ClassAliases::registerAliases();

$classLoaderDecorator = new Symplify\PHP7_CodeSniffer\Composer\ClassLoaderDecorator(
    new Symplify\PHP7_CodeSniffer\Standard\Finder\StandardFinder()
);
$classLoaderDecorator->decorate($classLoader);


// init tokens constants
new PHP_CodeSniffer\Util\Tokens();

define('PHP_CODESNIFFER_VERBOSITY', 1);
