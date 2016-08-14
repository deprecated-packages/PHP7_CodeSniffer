<?php

/*
 * This file is part of Symplify
 * Copyright (c) 2016 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\PHP7_CodeSniffer\Sniff\Factory;

use Nette\Utils\Strings;
use PHP_CodeSniffer\Sniffs\Sniff;
use SimpleXMLElement;
use Symplify\PHP7_CodeSniffer\Contract\Sniff\Factory\SniffFactoryInterface;
use Symplify\PHP7_CodeSniffer\Sniff\Naming\SniffNaming;
use Symplify\PHP7_CodeSniffer\Sniff\Sorter\SniffSorter;
use Symplify\PHP7_CodeSniffer\Sniff\Xml\DataCollector\CustomSniffPropertyDataCollector;
use Symplify\PHP7_CodeSniffer\Sniff\Xml\DataCollector\ExcludedSniffDataCollector;
use Symplify\PHP7_CodeSniffer\Sniff\SniffSetFactory;

final class RulesetXmlToSniffsFactory implements SniffFactoryInterface
{
    /**
     * @var SniffSetFactory
     */
    private $sniffSetFactory;

    /**
     * @var ExcludedSniffDataCollector
     */
    private $excludedSniffDataCollector;

    /**
     * @var CustomSniffPropertyDataCollector
     */
    private $customSniffPropertyDataCollector;

    public function __construct(
        ExcludedSniffDataCollector $excludedSniffDataCollector,
        CustomSniffPropertyDataCollector $customSniffPropertyDataCollector
    ) {
        $this->customSniffPropertyDataCollector = $customSniffPropertyDataCollector;
        $this->excludedSniffDataCollector = $excludedSniffDataCollector;
    }

    public function setSniffSetFactory(SniffSetFactory $sniffSetFactory)
    {
        $this->sniffSetFactory = $sniffSetFactory;
        $this->sniffSetFactory->addSniffFactory($this);
    }

    public function isMatch(string $reference) : bool
    {
        return Strings::endsWith($reference, 'ruleset.xml');
    }

    /**
     * @return Sniff[]
     */
    public function create(string $rulesetXmlFile) : array
    {
        $sniffs = [];

        $rulesetXml = simplexml_load_file($rulesetXmlFile);
        foreach ($rulesetXml->rule as $ruleXmlElement) {
            if ($this->isRuleXmlElementSkipped($ruleXmlElement)) {
                continue;
            }

            $this->excludedSniffDataCollector->collectFromRuleXmlElement($ruleXmlElement);
            $this->customSniffPropertyDataCollector->collectFromRuleXmlElement($ruleXmlElement);

            $sniffs = array_merge($sniffs, $this->sniffSetFactory->create($ruleXmlElement['ref']));
        }

        return SniffSorter::sort($sniffs);
    }

    private function isRuleXmlElementSkipped(SimpleXMLElement $ruleXmlElement) : bool
    {
        if (!isset($ruleXmlElement['ref'])) {
            return true;
        }

        if (isset($ruleXmlElement->severity)) {
            if (SniffNaming::isSniffCode($ruleXmlElement['ref'])) {
                return true;
            }

            return false;
        }

        return false;
    }
}
