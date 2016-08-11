<?php

/*
 * This file is part of Symplify
 * Copyright (c) 2016 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\PHP7_CodeSniffer\Contract\Ruleset\ToSniffNormalizer;

interface ToSniffNormalizerInterface
{
    public function isMatch(string $reference) : bool;

    public function normalize(string $reference) : array;
}
