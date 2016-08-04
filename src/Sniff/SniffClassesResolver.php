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

    public function resolveFromStandardsAndSniffs(
        array $standards,
        array $extraSniffs,
        array $excludedSniffs
    ) : array {
        $sniffs = [];
        $sniffs = $this->addStandardSniffs($sniffs, $standards);
        $sniffs = $this->addExtraSniffs($sniffs, $extraSniffs);
        return $this->removeExcludeSniffs($sniffs, $excludedSniffs);
    }

    private function addStandardSniffs(array $sniffs, array $standards) : array
    {
        $standards = $this->configurationResolver->resolve('standards', $standards);
        foreach ($standards as $rulesetXmlPath) {
            $sniffs = array_merge(
                $sniffs,
                $this->rulesetBuilder->buildFromRulesetXml($rulesetXmlPath)
            );
        }

        return $sniffs;
    }

    private function addExtraSniffs(array $sniffs, array $extraSniffs) : array
    {
        $extraSniffs = $this->configurationResolver->resolve('sniffs', $extraSniffs);
        return array_merge($sniffs, $extraSniffs);
    }

    private function removeExcludeSniffs(array $sniffs, array $excludedSniffs) : array
    {
        $excludedSniffs = $this->configurationResolver->resolve('sniffs', $excludedSniffs);

        foreach ($excludedSniffs as $sniffCode => $sniffClass) {
            if (isset($sniffs[$sniffCode])) {
                unset($sniffs[$sniffCode]);
            }
        }

        return $sniffs;
    }
}
