<?php

/*
 * This file is part of Symplify
 * Copyright (c) 2016 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\PHP7_CodeSniffer\Sniff;

use PHP_CodeSniffer\Sniffs\Sniff;
use Symplify\PHP7_CodeSniffer\Contract\Sniff\Factory\SniffFactoryInterface;

final class SniffSetFactory
{
    /**
     * @var SniffFactoryInterface[]
     */
    private $sniffFactories;

    public function addSniffFactory(SniffFactoryInterface $sniffFactory)
    {
        $this->sniffFactories[] = $sniffFactory;
    }

    /***
     * @param string[] $standardNames
     * @param string[] $sniffCodes
     * @return Sniff[]
     */
    public function createFromStandardsAndSniffs(
        array $standardNames,
        array $sniffCodes
    ) : array {
        $sniffs = [];
        $sniffs = $this->addStandardSniffs($sniffs, $standardNames);
        $sniffs = $this->addExtraSniffs($sniffs, $sniffCodes);
        return $sniffs;

    }

    public function create(string $source) : array
    {
        $sniffs = [];
        foreach ($this->sniffFactories as $sniffFactory) {
            if ($sniffFactory->isMatch($source)) {
                $newSniffs = $sniffFactory->create($source);
                $sniffs = array_merge($sniffs, $newSniffs);
            }
        }

        return $sniffs;
    }

    /**
     * @param Sniff[] $sniffs
     * @param string[] $standardNames
     * @return Sniff[]
     */
    private function addStandardSniffs(array $sniffs, array $standardNames) : array
    {
        foreach ($standardNames as $standardName) {
            $sniffs = array_merge(
                $sniffs,
                $this->create($standardName)
            );
        }

        return $sniffs;
    }

    /**
     * @param Sniff[] $sniffs
     * @param string[] $extraSniffs
     * @return Sniff[]
     */
    private function addExtraSniffs(array $sniffs, array $extraSniffs) : array
    {
        dump($extraSniffs);
        die;
//        $this->create($extraSniffs);
//        $extraSniffs = $this->configurationResolver->resolve('sniffs', $extraSniffs);
//        return array_merge($sniffs, $extraSniffs);
    }
}
