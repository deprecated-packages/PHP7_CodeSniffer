<?php

/*
 * This file is part of Symplify
 * Copyright (c) 2016 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\PHP7_CodeSniffer\Ruleset;

use SimpleXMLElement;
use Symplify\PHP7_CodeSniffer\Ruleset\Extractor\CustomPropertyValuesExtractor;
use Symplify\PHP7_CodeSniffer\Ruleset\Rule\ReferenceNormalizer;
use Symplify\PHP7_CodeSniffer\Sniff\Finder\SniffFinder;
use Symplify\PHP7_CodeSniffer\Standard\Finder\StandardFinder;

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
     * @var array
     */
    private $ruleset = [];

    /**
     * @var StandardFinder
     */
    private $standardFinder;

    /**
     * @var CustomPropertyValuesExtractor
     */
    private $customPropertyValuesExtractor;

    public function __construct(
        SniffFinder $sniffFinder,
        StandardFinder $standardFinder,
        ReferenceNormalizer $ruleReferenceNormalizer,
        CustomPropertyValuesExtractor $customPropertyValuesExtractor
    ) {
        $this->sniffFinder = $sniffFinder;
        $this->standardFinder = $standardFinder;
        $this->ruleReferenceNormalizer = $ruleReferenceNormalizer;
        $this->customPropertyValuesExtractor = $customPropertyValuesExtractor;
    }

    public function buildFromRulesetXml(string $rulesetXmlFile) : array
    {
        $rulesetXml = simplexml_load_file($rulesetXmlFile);

        $includedSniffs = [];
        $excludedSniffs = [];

        $this->ruleset = array_merge(
            $this->ruleset,
            $this->customPropertyValuesExtractor->extractFromRulesetXmlFile($rulesetXmlFile)
        );

        foreach ($rulesetXml->rule as $rule) {
            if (!isset($rule['ref'])) {
                continue;
            }

            $expandedSniffs = $this->normalizeReference($rule['ref']);
            $includedSniffs = array_merge($includedSniffs, $expandedSniffs);
            $excludedSniffs = $this->processExcludedRules($excludedSniffs, $rule);
        }

        $ownSniffs = $this->getOwnSniffsFromRuleset($rulesetXmlFile);
        $includedSniffs = array_unique(array_merge($ownSniffs, $includedSniffs));
        $excludedSniffs = array_unique($excludedSniffs);

        $sniffs = $this->filterOutExcludedSniffs($includedSniffs, $excludedSniffs);
        $sniffs = $this->sortSniffs($sniffs);

        // todo: decorate with custom rules!

        return $sniffs;
    }

    public function getRuleset() : array
    {
        return $this->ruleset;
    }

    private function normalizeReference(string $reference)
    {
        if ($this->ruleReferenceNormalizer->isStandardReference($reference)) {
            $ruleset = $this->standardFinder->getRulesetPathForStandardName($reference);
            return $this->buildFromRulesetXml($ruleset);
        }

        return $this->ruleReferenceNormalizer->normalize($reference);
    }

    /**
     * @return string[]
     */
    private function getOwnSniffsFromRuleset(string $rulesetXml) : array
    {
        $rulesetDir = dirname($rulesetXml);
        $sniffDir = $rulesetDir.DIRECTORY_SEPARATOR.'Sniffs';
        if (is_dir($sniffDir)) {
            return $this->sniffFinder->findAllSniffClassesInDirectory($sniffDir);
        }

        return [];
    }

    private function processExcludedRules($excludedSniffs, SimpleXMLElement $rule) : array
    {
        if (isset($rule->exclude)) {
            foreach ($rule->exclude as $exclude) {
                $excludedSniffs = array_merge(
                    $excludedSniffs,
                    $this->normalizeReference($exclude['name'])
                );
            }
        }

        return $excludedSniffs;
    }

    private function filterOutExcludedSniffs(array $includedSniffs, array $excludedSniffs) : array
    {
        $sniffs = [];
        foreach ($includedSniffs as $sniffCode => $sniffClass) {
            if (!in_array($sniffCode, $excludedSniffs)) {
                $sniffs[$sniffCode] = $sniffClass;
            }
        }

        return $sniffs;
    }

    private function sortSniffs(array $sniffs) : array
    {
        ksort($sniffs);
        return $sniffs;
    }
}
