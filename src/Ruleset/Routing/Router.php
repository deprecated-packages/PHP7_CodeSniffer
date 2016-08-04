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

    public function getClassFromSniffCode(string $sniffCode) : string
    {
        $sniffCode = $this->normalizeToSniffClassCode($sniffCode);

        if (isset($this->foundClasses[$sniffCode])) {
            return $this->foundClasses[$sniffCode];
        }

        $sniffClasses = $this->sniffFinder->findAllSniffClasses();
        if (isset($sniffClasses[$sniffCode])) {
            return $sniffClasses[$sniffCode];
        }

        return $this->foundClasses[$sniffCode] = $this->findClosesMatch($sniffClasses, $sniffCode);
    }

    private function findClosesMatch(array $sniffClasses, string $seekedSniffCode)
    {
        $shortestDistance = -1;
        $closest = '';
        foreach ($sniffClasses as $sniffCode => $sniffClass) {
            $levenshtein = levenshtein($seekedSniffCode, $sniffClass);
            if ($levenshtein <= $shortestDistance || $shortestDistance < 0) {
                $closest = $sniffClass;
                $shortestDistance = $levenshtein;
            }
        }

        if ($shortestDistance <= 30) {
            return $closest;
        }

        return '';
    }

    private function normalizeToSniffClassCode(string $sniffCode) : string
    {
        $parts = explode('.', $sniffCode);
        if (count($parts) === 4) {
            return $parts[0].'.'.$parts[1].'.'.$parts[2];
        }

        return $sniffCode;
    }
}
