<?php

/*
 * This file is part of Symplify
 * Copyright (c) 2016 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\PHP7_CodeSniffer\Sniff\Factory;

use PHP_CodeSniffer\Sniffs\Sniff;
use Symplify\PHP7_CodeSniffer\Sniff\Xml\DataCollector\CustomSniffPropertyValueDataCollector;
use Symplify\PHP7_CodeSniffer\Sniff\Xml\DataCollector\ExcludedSniffDataCollector;

final class SingleSniffFactory
{
    /**
     * @var ExcludedSniffDataCollector
     */
    private $excludedSniffDataCollector;

    /**
     * @var CustomSniffPropertyValueDataCollector
     */
    private $customSniffPropertyDataCollector;

    public function __construct(
        ExcludedSniffDataCollector $excludedSniffDataCollector,
        CustomSniffPropertyValueDataCollector $customSniffPropertyDataCollector
    ) {
        $this->excludedSniffDataCollector = $excludedSniffDataCollector;
        $this->customSniffPropertyDataCollector = $customSniffPropertyDataCollector;
    }

    /**
     * @return Sniff|null
     */
    public function create(string $sniffClassName)
    {
        if ($this->excludedSniffDataCollector->isSniffClassExcluded($sniffClassName)) {
            return null;
        }

        $sniff = new $sniffClassName;
        return $this->setCustomSniffPropertyValues($sniff);
    }

    private function setCustomSniffPropertyValues(Sniff $sniff) : Sniff
    {
        $sniffClassName = get_class($sniff);
        if ($customSniffPropertyValues = $this->customSniffPropertyDataCollector->getForSniffClass($sniffClassName)) {
            foreach ($customSniffPropertyValues as $property => $value) {
                $sniff->$property = $value;
            }
        }

        return $sniff;
    }
}
