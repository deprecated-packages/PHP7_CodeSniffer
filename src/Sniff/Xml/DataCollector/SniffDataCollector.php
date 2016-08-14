<?php

/*
 * This file is part of Symplify
 * Copyright (c) 2016 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\PHP7_CodeSniffer\Sniff\Xml\DataCollector;

use PHP_CodeSniffer\Sniffs\Sniff;
use Symplify\PHP7_CodeSniffer\Sniff\Sorter\SniffSorter;

final class SniffDataCollector
{
    /**
     * @var Sniff[]
     */
    private $sniffs = [];

    public function addSniff(Sniff $sniff)
    {
        $this->sniffs[] = $sniff;
    }

    /**
     * @return Sniff[]
     */
    public function getSniffs() : array
    {
        return $this->sniffs = SniffSorter::sort($this->sniffs);
    }
}
