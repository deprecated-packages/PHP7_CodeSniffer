<?php

/*
 * This file is part of Symplify
 * Copyright (c) 2016 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\PHP7_CodeSniffer\Configuration;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Symplify\PHP7_CodeSniffer\Exception\Configuration\InvalidSniffCodeException;
use Symplify\PHP7_CodeSniffer\Exception\Configuration\SourceNotFoundException;
use Symplify\PHP7_CodeSniffer\Exception\Configuration\StandardNotFoundException;
use Symplify\PHP7_CodeSniffer\Standard\StandardFinder;

final class ConfigurationResolver
{
    /**
     * @var StandardFinder
     */
    private $standardFinder;

    /**
     * @var OptionsResolver
     */
    private $optionsResolver;

    public function __construct(StandardFinder $standardFinder)
    {
        $this->standardFinder = $standardFinder;
        $this->createAndSetupOptionsResolver();
    }

    public function resolve(array $options) : array
    {
        return $this->optionsResolver->resolve($options);
    }

    private function createAndSetupOptionsResolver()
    {
        $this->optionsResolver = new OptionsResolver();
        $this->setDefaults();
        $this->setRequired();
        $this->setAllowedValues();
        $this->setNormalizers();
    }

    private function setDefaults()
    {
        $this->optionsResolver->setDefaults([
            'fix' => false,
            'source' => [],
            'standards' => ['PSR2'],
            'sniffs' => []
        ]);
    }

    private function setRequired()
    {
        $this->optionsResolver->setRequired(['source', 'standards']);
    }

    private function setAllowedValues()
    {
        $this->optionsResolver->setAllowedValues('standards', function (array $standards) {
            $standards = $this->normalizeCommaSeparatedValues($standards);

            $availableStandards = $this->standardFinder->getStandards();
            foreach ($standards as $standardName) {
                if (!array_key_exists($standardName, $availableStandards)) {
                    throw new StandardNotFoundException(sprintf(
                        'Standard "%s" is not supported. Pick one of: %s',
                        $standardName,
                        implode(array_keys($availableStandards), ', ')
                    ));
                }
            }

            return true;
        });

        $this->optionsResolver->setAllowedValues('sniffs', function (array $sniffs) {
            $sniffs = $this->normalizeCommaSeparatedValues($sniffs);

            foreach ($sniffs as $sniff) {
                if (substr_count($sniff, '.') !== 2) {
                    throw new InvalidSniffCodeException(sprintf(
                        'The specified sniff code "%s" is invalid.'.
                        PHP_EOL.
                        'Correct format is "StandardName.Category.SniffName".',
                        $sniff
                    ));
                }
            }

            return true;
        });

        $this->optionsResolver->setAllowedValues('source', function (array $source) {
            foreach ($source as $singleSource) {
                if (!file_exists($singleSource)) {
                    throw new SourceNotFoundException(sprintf(
                        'Source "%s" does not exist.',
                        $singleSource
                    ));
                }
            }

            return true;
        });
    }

    private function setNormalizers()
    {
        $this->optionsResolver->setNormalizer('standards', function (OptionsResolver $optionsResolver, array $standardNames) {
            $standardNames = $this->normalizeCommaSeparatedValues($standardNames);

            return $this->standardFinder->getRulesetPathsForStandardNames($standardNames);
        });
    }

    private function normalizeCommaSeparatedValues(array $values) : array
    {
        $newValues = [];
        foreach ($values as $value) {
            $newValues = array_merge($newValues, explode(',', $value));
        }

        return $newValues;
    }
}
