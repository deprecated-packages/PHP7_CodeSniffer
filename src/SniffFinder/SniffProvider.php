<?php

/*
 * This file is part of Symplify
 * Copyright (c) 2016 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\PHP7_CodeSniffer\SniffFinder;

use Symplify\PHP7_CodeSniffer\Configuration\Configuration;
use Symplify\PHP7_CodeSniffer\Ruleset\Routing\Router;
use Symplify\PHP7_CodeSniffer\Ruleset\RulesetBuilder;
use Symplify\PHP7_CodeSniffer\SniffFinder\Composer\VendorDirProvider;
use Symplify\PHP7_CodeSniffer\SniffFinder\Contract\SniffFinderInterface;

final class SniffProvider
{
    /**
     * @var Configuration
     */
    private $configuration;

    /**
     * @var RulesetBuilder
     */
    private $rulesetBuilder;

    /**
     * @var Router
     */
    private $router;

    public function __construct(Configuration $configuration, RulesetBuilder $rulesetBuilder, Router $router)
    {
        $this->configuration = $configuration;
        $this->rulesetBuilder = $rulesetBuilder;
        $this->router = $router;
    }

    public function getActiveSniffs() : array
    {
        $sniffs = [];
        foreach ($this->configuration->getStandards() as $name => $rulesetXmlPath) {
            $newSniffs = $this->rulesetBuilder->buildFromRulesetXml($rulesetXmlPath);
            $sniffs = array_merge($sniffs, $newSniffs);
        }

        if ($sniffRestrictions = $this->getSniffRestrictions()) {
            $this->excludeRestrictedSniffs($sniffs, $sniffRestrictions);
        }

        return $sniffs;
    }

    private function getSniffRestrictions() : array
    {
        return $this->configuration->getSniff();
//        $sniffRestrictions = [];
//        foreach ($this->configuration->getSniff() as $sniffName) {
//            $sniffClass = $this->router->getClassFromSniffName($sniffName);
//            $sniffRestrictions[$sniffName] = $sniffClass;
//        }
//
//        return $sniffRestrictions;
    }

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
