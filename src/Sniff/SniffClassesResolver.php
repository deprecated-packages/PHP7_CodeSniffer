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

    /**
     * @param string[] $standards
     * @param string[] $extraSniffs
     * @return string[]
     */
    public function resolveFromStandardsAndSniffs(array $standards, array $extraSniffs) : array
    {
        $standards = $this->configurationResolver->resolve('standards', $standards);

        $sniffs = [];
        foreach ($standards as $rulesetXmlPath) {
            $sniffs = array_merge(
                $sniffs,
                $this->rulesetBuilder->buildFromRulesetXml($rulesetXmlPath)
            );
        }

        $extraSniffs = $this->configurationResolver->resolve('sniffs', $extraSniffs);
        return $this->mergeSniffs($sniffs, $extraSniffs);
    }

    private function mergeSniffs(array $sniffs, array $extraSniffs) : array
    {
        $sniffs = array_merge($sniffs, $extraSniffs);
        return $sniffs;
    }
}
