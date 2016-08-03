<?php

/*
 * This file is part of Symplify
 * Copyright (c) 2016 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\PHP7_CodeSniffer\Sniff;

use PHP_CodeSniffer\Sniffs\Sniff;

final class SniffFactory
{
    /**
     * @param string[] $sniffClasses
     * @return Sniff[]
     */
    public function createFromSniffClassNames(array $sniffClasses) : array
    {
        $sniffs = [];
        foreach ($sniffClasses as $sniffCode => $sniffClass) {
            $sniffs[$sniffCode] = new $sniffClass;
        }

        return $sniffs;
    }
}
