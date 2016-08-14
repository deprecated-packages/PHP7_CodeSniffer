<?php

/*
 * This file is part of Symplify
 * Copyright (c) 2016 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\PHP7_CodeSniffer\Sniff\Factory;

use Symplify\PHP7_CodeSniffer\Contract\Sniff\Factory\SniffFactoryInterface;
use Symplify\PHP7_CodeSniffer\Standard\Finder\StandardFinder;

final class StandardNameToSniffsFactory implements SniffFactoryInterface
{
    /**
     * @var StandardFinder
     */
    private $standardFinder;

    /**
     * @var RulesetXmlToSniffsFactory
     */
    private $rulesetXmlToSniffsFactory;

    /**
     * @var RulesetXmlToOwnSniffsFactory
     */
    private $rulesetXmlToOwnSniffsFactory;

    public function __construct(
        StandardFinder $standardFinder,
        RulesetXmlToSniffsFactory $rulesetXmlToSniffsFactory,
        RulesetXmlToOwnSniffsFactory $rulesetXmlToOwnSniffsFactory
    ) {
        $this->standardFinder = $standardFinder;
        $this->rulesetXmlToSniffsFactory = $rulesetXmlToSniffsFactory;
        $this->rulesetXmlToOwnSniffsFactory = $rulesetXmlToOwnSniffsFactory;
    }

    public function isMatch(string $reference) : bool
    {
        $standards = $this->standardFinder->getStandards();
        return (isset($standards[$reference]));
    }

    public function create(string $standardName) : array
    {
        $rulesetXml = $this->standardFinder->getRulesetPathForStandardName($standardName);
        $rulesetSniffs = $this->rulesetXmlToSniffsFactory->create($rulesetXml);

        $rulesetOwnSniffs = $this->rulesetXmlToOwnSniffsFactory->create($rulesetXml);

        return array_merge($rulesetSniffs, $rulesetOwnSniffs);
    }
}