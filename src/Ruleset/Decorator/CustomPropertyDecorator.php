<?php

/*
 * This file is part of Symplify
 * Copyright (c) 2016 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\PHP7_CodeSniffer\Ruleset\Decorator;

use PHP_CodeSniffer\Sniffs\Sniff;

final class CustomPropertyDecorator
{
    /**
     * @param Sniff[] $sniffs
     * @param array $customProperties
     * @return Sniff[]
     */
    public function decorateSniffs(array $sniffs, array $customProperties) : array
    {
        return $sniffs;
    }
}
