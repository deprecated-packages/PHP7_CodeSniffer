<?php

/*
 * This file is part of Symplify
 * Copyright (c) 2016 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\PHP7_CodeSniffer\Ruleset\Rule;

use Nette\Utils\Strings;
use Symplify\PHP7_CodeSniffer\Ruleset\Routing\Router;
use Symplify\PHP7_CodeSniffer\Sniff\Finder\SniffFinder;
use Symplify\PHP7_CodeSniffer\Standard\Finder\StandardFinder;

final class ReferenceNormalizer
{
    /**
     * @var SniffFinder
     */
    private $sniffFinder;

    /**
     * @var StandardFinder
     */
    private $standardFinder;

    /**
     * @var Router
     */
    private $router;

    public function __construct(
        SniffFinder $sniffFinder,
        StandardFinder $standardFinder,
        Router $router
    ) {
        $this->sniffFinder = $sniffFinder;
        $this->standardFinder = $standardFinder;
        $this->router = $router;
    }

    public function normalize(string $reference) : array
    {
        return [
            $reference => $this->normalizeSniffNameToClass($reference)
        ];
    }

    public function isStandardReference(string $reference) : bool
    {
        $standards = $this->standardFinder->getStandards();
        if (isset($standards[$reference])) {
            return true;
        }

        return false;
    }

    private function normalizeSniffNameToClass(string $name) : string
    {
        return $this->router->getClassFromSniffCode($name);
    }
}
