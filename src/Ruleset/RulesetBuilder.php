<?php

/*
 * This file is part of Symplify
 * Copyright (c) 2016 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\PHP7_CodeSniffer\Ruleset;

use Symplify\PHP7_CodeSniffer\Ruleset\Extractor\CustomPropertyValuesExtractor;
use Symplify\PHP7_CodeSniffer\Ruleset\Rule\ReferenceNormalizer;
use Symplify\PHP7_CodeSniffer\Sniff\Finder\SniffFinder;

final class RulesetBuilder
{
    /**
     * @var SniffFinder
     */
    private $sniffFinder;

    /**
     * @var ReferenceNormalizer
     */
    private $ruleReferenceNormalizer;

    /**
     * @var CustomPropertyValuesExtractor
     */
    private $customPropertyValuesExtractor;

    public function __construct(
        SniffFinder $sniffFinder,
        ReferenceNormalizer $ruleReferenceNormalizer,
        CustomPropertyValuesExtractor $customPropertyValuesExtractor
    ) {
        $this->sniffFinder = $sniffFinder;
        $this->ruleReferenceNormalizer = $ruleReferenceNormalizer;
        $this->customPropertyValuesExtractor = $customPropertyValuesExtractor;
    }

    public function buildFromRulesetXml(string $rulesetXmlFile) : array
    {
        $rulesetXml = simplexml_load_file($rulesetXmlFile);

        $sniffs = $this->ruleReferenceNormalizer->normalize($rulesetXml);

        $sniffs = $this->sortSniffs($sniffs);

        // todo: decorate with custom rules!

        return $sniffs;
    }

    private function sortSniffs(array $sniffs) : array
    {
        ksort($sniffs);
        return $sniffs;
    }
}
