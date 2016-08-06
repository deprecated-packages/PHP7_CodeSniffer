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
     * @param array[][][] $customProperties
     * @return Sniff[]
     */
    public function createFromSniffClassNames(array $sniffClasses, array $customProperties=[]) : array
    {
        $sniffs = [];
        foreach ($sniffClasses as $sniffCode => $sniffClass) {
            $sniffs[$sniffCode] = new $sniffClass;
        }

        //        dump($customProperties);
        //        die;

        return $sniffs;
    }
}
