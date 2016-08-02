<?php

/*
 * This file is part of Symplify
 * Copyright (c) 2016 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\PHP7_CodeSniffer\SniffFinder;

use Symplify\PHP7_CodeSniffer\Configuration\ConfigurationResolver;
use Symplify\PHP7_CodeSniffer\Ruleset\RulesetBuilder;

final class SniffProvider
{
    /**
     * @var ConfigurationResolver
     */
    private $configurationResolver;

    /**
     * @var RulesetBuilder
     */
    private $rulesetBuilder;

    public function __construct(ConfigurationResolver $configurationResolver, RulesetBuilder $rulesetBuilder)
    {
        $this->configurationResolver = $configurationResolver;
        $this->rulesetBuilder = $rulesetBuilder;
    }

    public function getActiveSniffs(array $standards, array $sniffs) : array
    {
        $standards = $this->configurationResolver->resolveStandards($standards);
        $exclusivelyIncludedSniffs = $this->configurationResolver->resolveSniffs($sniffs);
        
        $sniffs = [];
        foreach ($standards as $rulesetXmlPath) {
            $newSniffs = $this->rulesetBuilder->buildFromRulesetXml($rulesetXmlPath);
            $sniffs = array_merge($sniffs, $newSniffs);
        }

        if ($exclusivelyIncludedSniffs) {
            $this->excludeRestrictedSniffs($sniffs, $exclusivelyIncludedSniffs);
        }

        return $sniffs;
    }

    private function excludeRestrictedSniffs(array $sniffs, array $exclusivelyIncludedSniffs)
    {
        $finalSniffs = [];
        foreach ($sniffs as $sniffCode => $sniffClass) {
            if (isset($exclusivelyIncludedSniffs[$sniffCode]) === false) {
                continue;
            }

            $finalSniffs[$sniffCode] = $sniffClass;
        }

        return $finalSniffs;
    }
}
