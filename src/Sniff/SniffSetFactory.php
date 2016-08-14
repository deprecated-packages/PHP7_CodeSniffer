<?php

/*
 * This file is part of Symplify
 * Copyright (c) 2016 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\PHP7_CodeSniffer\Sniff;

use PHP_CodeSniffer\Sniffs\Sniff;
use Symplify\PHP7_CodeSniffer\Configuration\ConfigurationResolver;
use Symplify\PHP7_CodeSniffer\Contract\Sniff\Factory\SniffFactoryInterface;

final class SniffSetFactory
{
    /**
     * @var ConfigurationResolver
     */
    private $configurationResolver;

    /**
     * @var SniffFactoryInterface[]
     */
    private $sniffFactories;

//    public function __construct(ConfigurationResolver $configurationResolver)
//    {
//        $this->configurationResolver = $configurationResolver;
//    }

    public function addSniffFactory(SniffFactoryInterface $sniffFactory)
    {
        $this->sniffFactories[] = $sniffFactory;
    }

    /***
     * @param string[] $standards
     * @param string[] $extraSniffs
     * @return Sniff[]
     */
    public function createFromStandardsAndSniffs(
        array $standards,
        array $extraSniffs
    ) : array {
        $sniffClassNames = [];
        $sniffClassNames = $this->addStandardSniffs($sniffClassNames, $standards);
        $sniffClassNames = $this->addExtraSniffs($sniffClassNames, $extraSniffs);

        dump($sniffClassNames);
        die;

        return $this->create($sniffClassNames);
    }

    public function create(string $source) : array
    {
        $sniffs = [];
        foreach ($this->sniffFactories as $sniffFactory) {
            if ($sniffFactory->isMatch($source)) {
                $sniffs = array_merge($sniffs, $sniffFactory->create($source));
            }
        }

        return $sniffs;
    }

    private function addStandardSniffs(array $sniffs, array $standards) : array
    {
        dump($standards);
        die;

//        $standards = $this->configurationResolver->resolve('standards', $standards);
        foreach ($standards as $rulesetXmlPath) {
            $sniffs = array_merge(
                $sniffs,
                $this->create($rulesetXmlPath)
            );
        }

        return $sniffs;
    }

    private function addExtraSniffs(array $sniffs, array $extraSniffs) : array
    {
//        $extraSniffs = $this->configurationResolver->resolve('sniffs', $extraSniffs);
        return array_merge($sniffs, $extraSniffs);
    }
}
