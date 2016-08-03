<?php

/*
 * This file is part of Symplify
 * Copyright (c) 2016 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\PHP7_CodeSniffer\Sniff;

use Symplify\PHP7_CodeSniffer\Configuration\ConfigurationResolver;
use Symplify\PHP7_CodeSniffer\Ruleset\RulesetBuilder;

final class SniffClassesResolver
{
    /**
     * @var ConfigurationResolver
     */
    private $configurationResolver;

    /**
     * @var RulesetBuilder
     */
    private $rulesetBuilder;

    public function __construct(
        ConfigurationResolver $configurationResolver,
        RulesetBuilder $rulesetBuilder
    ) {
        $this->configurationResolver = $configurationResolver;
        $this->rulesetBuilder = $rulesetBuilder;
    }

    public function resolveFromStandardsAndSniffs(array $standards, array $includedSniffs) : array
    {
        $standards = $this->configurationResolver->resolveStandards($standards);
        $includedSniffs = $this->configurationResolver->resolveSniffs($includedSniffs);

        $sniffs = [];
        foreach ($standards as $rulesetXmlPath) {
            $newSniffs = $this->rulesetBuilder->buildFromRulesetXml($rulesetXmlPath);
            $sniffs = array_merge($sniffs, $newSniffs);
        }

        if ($includedSniffs) {
            $sniffs = $this->excludeRestrictedSniffs($sniffs, $includedSniffs);
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
