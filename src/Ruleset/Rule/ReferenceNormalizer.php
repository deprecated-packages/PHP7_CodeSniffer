<?php

/*
 * This file is part of Symplify
 * Copyright (c) 2016 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\PHP7_CodeSniffer\Ruleset\Rule;

use Symplify\PHP7_CodeSniffer\Contract\Ruleset\ToSniffNormalizer\ToSniffNormalizerInterface;

final class ReferenceNormalizer
{
    /**
     * @var ToSniffNormalizerInterface[]
     */
    private $toSniffNormalizers = [];

    public function addNormalizer(ToSniffNormalizerInterface $toSniffNormalizer)
    {
        $this->toSniffNormalizers[] = $toSniffNormalizer;
    }

    public function normalize(string $reference) : array
    {
        foreach ($this->toSniffNormalizers as $toSniffNormalizer) {
            if ($toSniffNormalizer->isMatch($reference)) {
                return $toSniffNormalizer->normalize($reference);
            }
        }

        throw new \Exception(sprintf(
            'Refernce "%s" is not supported yet.',
            $reference
        ));
    }
}
