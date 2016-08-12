<?php

/*
 * This file is part of Symplify
 * Copyright (c) 2016 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\PHP7_CodeSniffer\Sniff;

use PHP_CodeSniffer\Sniffs\Sniff;
use Symplify\PHP7_CodeSniffer\Configuration\ConfigurationResolver;
use Symplify\PHP7_CodeSniffer\Ruleset\ToSniffNormalizer\RulesetXmlToSniffNormalizer;

final class SniffFactory
{
    /**
     * @var ConfigurationResolver
     */
    private $configurationResolver;

    /**
     * @var RulesetXmlToSniffNormalizer
     */
    private $rulesetXmlToSniffNormalizer;

    public function __construct(
        ConfigurationResolver $configurationResolver,
        RulesetXmlToSniffNormalizer $rulesetXmlToSniffNormalizer
    ) {
        $this->configurationResolver = $configurationResolver;
        $this->rulesetXmlToSniffNormalizer = $rulesetXmlToSniffNormalizer;
    }

    /***
     * @param string[] $standards
     * @param string[] $extraSniffs
     * @param string[] $excludedSniffs
     * @return Sniff[]
     */
    public function createFromStandardsAndSniffs(
        array $standards,
        array $extraSniffs,
        array $excludedSniffs
    ) : array {
        $sniffClasses = [];
        $sniffClasses = $this->addStandardSniffs($sniffClasses, $standards);
        $sniffClasses = $this->addExtraSniffs($sniffClasses, $extraSniffs);
        $sniffClassNames = $this->removeExcludeSniffs($sniffClasses, $excludedSniffs);

        $sniffs = [];
        foreach ($sniffClassNames as $sniffCode => $sniffClassName) {
            $sniffs[$sniffCode] = new $sniffClassName;
        }

        // todo: decorate with custom properties
        // or hide in normalizer?

        return $sniffs;
    }

    private function addStandardSniffs(array $sniffs, array $standards) : array
    {
        $standards = $this->configurationResolver->resolve('standards', $standards);
        foreach ($standards as $rulesetXmlPath) {
            $sniffs = array_merge(
                $sniffs,
                $this->rulesetXmlToSniffNormalizer->normalize($rulesetXmlPath)
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
