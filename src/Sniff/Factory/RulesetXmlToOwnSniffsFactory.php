<?php

/*
 * This file is part of Symplify
 * Copyright (c) 2016 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\PHP7_CodeSniffer\Sniff\Factory;

use Nette\Utils\Strings;
use PHP_CodeSniffer\Sniffs\Sniff;
use Symplify\PHP7_CodeSniffer\Contract\Sniff\Factory\SniffFactoryInterface;
use Symplify\PHP7_CodeSniffer\Sniff\Finder\SniffFinder;

final class RulesetXmlToOwnSniffsFactory implements SniffFactoryInterface
{
    /**
     * @var SniffFinder
     */
    private $sniffFinder;

    /**
     * @var SingleSniffFactory
     */
    private $singleSniffFactory;

    public function __construct(SniffFinder $sniffFinder, SingleSniffFactory $singleSniffFactory)
    {
        $this->sniffFinder = $sniffFinder;
        $this->singleSniffFactory = $singleSniffFactory;
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
        $rulesetDir = dirname($rulesetXmlFile);
        $sniffDir = $rulesetDir.DIRECTORY_SEPARATOR.'Sniffs';
        if (!is_dir($sniffDir)) {
            return [];
        }

        $sniffClassNames = $this->sniffFinder->findAllSniffClassesInDirectory($sniffDir);

        $sniffs = [];
        foreach ($sniffClassNames as $sniffClassName) {
            if ($sniff = $this->singleSniffFactory->create($sniffClassName)) {
                $sniffs[] = $sniff;
            }
        }

        return $sniffs;
    }
}
