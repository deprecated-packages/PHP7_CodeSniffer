<?php

/*
 * This file is part of Symplify
 * Copyright (c) 2016 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\PHP7_CodeSniffer\Ruleset\Routing;

use Symplify\PHP7_CodeSniffer\Sniff\Finder\SniffFinder;

final class Router
{
    /**
     * @var SniffFinder
     */
    private $sniffFinder;

    /**
     * @var string[]
     */
    private $foundClasses = [];

    public function __construct(SniffFinder $sniffFinder)
    {
        $this->sniffFinder = $sniffFinder;
    }

    public function getClassFromSniffName(string $sniffName) : string
    {
        if (isset($this->foundClasses[$sniffName])) {
            return $this->foundClasses[$sniffName];
        }

        $sniffClasses = $this->sniffFinder->findAllSniffClasses();
        $sniffClass = $this->findClosesMatch($sniffClasses, $sniffName);

        return $this->foundClasses[$sniffName] = $sniffClass;
    }

    private function findClosesMatch(array $words, string $input)
    {
        $shortestDistance = -1;
        $closest = '';
        foreach ($words as $word) {
            $levenshtein = levenshtein($input, $word);
            if ($levenshtein <= $shortestDistance || $shortestDistance < 0) {
                $closest = $word;
                $shortestDistance = $levenshtein;
            }
        }

        if ($shortestDistance <= 60) {
            return $closest;
        }

        return '';
    }
}
