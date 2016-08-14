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

    public function __construct(ConfigurationResolver $configurationResolver)
    {
        $this->configurationResolver = $configurationResolver;
    }

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
//        array $excludedSniffs
    ) : array {
        $sniffClassNames = [];
        $sniffClassNames = $this->addStandardSniffs($sniffClassNames, $standards);
        $sniffClassNames = $this->addExtraSniffs($sniffClassNames, $extraSniffs);
//        $sniffClassNames = $this->removeExcludeSniffs($sniffClassNames, $excludedSniffs);

//        foreach ($sniffClassNames as $sniffClassName) {
        return $this->create($sniffClassNames);
//        }

        // here only resolve class names and that stuffs
//        return $this->createSniffsFromSniffClassNames($sniffClassNames);
    }

    public function create(string $source) : array
    {
        foreach ($this->sniffFactories as $sniffFactory) {
            if ($sniffFactory->isMatch($source)) {
                return $sniffFactory->create($source);
            }
        }

        throw new \Exception(sprintf(
            'Factory for "%s" type is not supported yet.',
            $source
        ));
    }

    private function addStandardSniffs(array $sniffs, array $standards) : array
    {
        $standards = $this->configurationResolver->resolve('standards', $standards);
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
        $extraSniffs = $this->configurationResolver->resolve('sniffs', $extraSniffs);
        return array_merge($sniffs, $extraSniffs);
    }

//    /**
//     * @param string[] $sniffClassNames
//     * @return Sniff[]
//     */
//    private function createSniffsFromSniffClassNames(array $sniffClassNames) : array
//    {
//        $sniffs = [];
//        foreach ($sniffClassNames as $sniffCode => $sniffClassName) {
//            $sniffs[$sniffCode] = new $sniffClassName;
//        }
//        return $sniffs;
//    }
}
