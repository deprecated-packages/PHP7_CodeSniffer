<?php

/*
 * This file is part of Symplify
 * Copyright (c) 2016 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\PHP7_CodeSniffer\Legacy;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

final class ClassAliases
{
    public static function registerAliases()
    {
        class_alias(File::class, 'PHP_CodeSniffer_File');
        class_alias(Sniff::class, 'PHP_CodeSniffer_Sniff');
    }
}
