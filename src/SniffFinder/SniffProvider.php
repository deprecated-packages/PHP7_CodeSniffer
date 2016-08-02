<?php

/*
 * This file is part of Symplify
 * Copyright (c) 2016 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\PHP7_CodeSniffer\SniffFinder;

use Symplify\PHP7_CodeSniffer\Configuration\ConfigurationResolver;
use Symplify\PHP7_CodeSniffer\Ruleset\Routing\Router;
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

    /**
     * @var Router
     */
    private $router;

    public function __construct(ConfigurationResolver $configurationResolver, RulesetBuilder $rulesetBuilder, Router $router)
    {
        $this->configurationResolver = $configurationResolver;
        $this->rulesetBuilder = $rulesetBuilder;
        $this->router = $router;
    }

    public function getActiveSniffs(array $standards, array $sniffs) : array
    {
        $standards = $this->configurationResolver->resolveStandards($standards);
        $sniffs = $this->configurationResolver->resolveSniffs($sniffs);
        
        $sniffs = [];
        foreach ($standards as $rulesetXmlPath) {
            $newSniffs = $this->rulesetBuilder->buildFromRulesetXml($rulesetXmlPath);
            $sniffs = array_merge($sniffs, $newSniffs);
        }

        if ($sniffRestrictions = $this->getSniffRestrictions()) {
            $this->excludeRestrictedSniffs($sniffs, $sniffRestrictions);
        }

        return $sniffs;
    }

//    private function getSniffRestrictions() : array
//    {
//        return $this->configuration->getSniff();
//    }

    private function excludeRestrictedSniffs(array $sniffs, array $sniffsToBeKept)
    {
        $finalSniffs = [];
        foreach ($sniffs as $sniffCode => $sniffClass) {
            if (isset($sniffsToBeKept[$sniffCode]) === false) {
                continue;
            }

            $finalSniffs[$sniffCode] = $sniffClass;
        }

        return $finalSniffs;
    }
}
