<?php

/*
 * This file is part of Symplify
 * Copyright (c) 2016 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\PHP7_CodeSniffer\Sniff;

use PHP_CodeSniffer\Sniffs\Sniff;
use Symplify\PHP7_CodeSniffer\Configuration\ConfigurationResolver;
use Symplify\PHP7_CodeSniffer\Ruleset\Rule\ReferenceNormalizer;

final class SniffFactory
{
    /**
     * @var ConfigurationResolver
     */
    private $configurationResolver;

    /**
     * @var ReferenceNormalizer
     */
    private $referenceNormalizer;

    public function __construct(
        ConfigurationResolver $configurationResolver,
        ReferenceNormalizer $referenceNormalizer
    ) {
        $this->configurationResolver = $configurationResolver;
        $this->referenceNormalizer = $referenceNormalizer;
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
        $sniffClassNames = [];
        $sniffClassNames = $this->addStandardSniffs($sniffClassNames, $standards);
        $sniffClassNames = $this->addExtraSniffs($sniffClassNames, $extraSniffs);
        $sniffClassNames = $this->removeExcludeSniffs($sniffClassNames, $excludedSniffs);

        $sniffs = $this->createSniffsFromSniffClassNames($sniffClassNames);

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
                $this->referenceNormalizer->normalize($rulesetXmlPath)
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

    /**
     * @param string[] $sniffClassNames
     * @return Sniff[]
     */
    private function createSniffsFromSniffClassNames(array $sniffClassNames) : array
    {
        $sniffs = [];
        foreach ($sniffClassNames as $sniffCode => $sniffClassName) {
            $sniffs[$sniffCode] = new $sniffClassName;
        }
        return $sniffs;
    }
}
