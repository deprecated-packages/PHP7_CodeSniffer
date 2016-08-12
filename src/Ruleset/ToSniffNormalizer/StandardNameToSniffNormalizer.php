<?php

/*
 * This file is part of Symplify
 * Copyright (c) 2016 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\PHP7_CodeSniffer\Ruleset\ToSniffNormalizer;

use Symplify\PHP7_CodeSniffer\Contract\Ruleset\ToSniffNormalizer\ToSniffNormalizerInterface;
use Symplify\PHP7_CodeSniffer\Standard\Finder\StandardFinder;

final class StandardNameToSniffNormalizer implements ToSniffNormalizerInterface
{
    /**
     * @var StandardFinder
     */
    private $standardFinder;

    /**
     * @var RulesetXmlToSniffNormalizer
     */
    private $rulesetXmlToSniffNormalizer;

    public function __construct(StandardFinder $standardFinder, RulesetXmlToSniffNormalizer $rulesetXmlToSniffNormalizer)
    {
        $this->standardFinder = $standardFinder;
        $this->rulesetXmlToSniffNormalizer = $rulesetXmlToSniffNormalizer;
    }

    public function isMatch(string $reference) : bool
    {
        $standards = $this->standardFinder->getStandards();
        return (isset($standards[$reference]));
    }

    public function normalize(string $reference) : array
    {
        $rulesetXml = $this->standardFinder->getRulesetPathForStandardName($reference);
        return $this->rulesetXmlToSniffNormalizer->normalize($rulesetXml);
    }
}