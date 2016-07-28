<?php

/*
 * This file is part of Symplify
 * Copyright (c) 2016 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\PHP7_CodeSniffer\SniffFinder\Naming;

use Nette\Utils\Strings;

final class SniffNaming
{
    public static function guessSniffClassBySniffCode(string $sniffCode) : string
    {
        $parts = explode('.', $sniffCode);

        $firstGuess = $parts[0].'CodingStandard\\Sniffs\\'.$parts[1].'\\'.$parts[2].'Sniff';
        if (class_exists($firstGuess)) {
            return $firstGuess;
        }

        $secondGuess = $parts[0].'\\CodingStandard\\Sniffs\\'.$parts[1].'\\'.$parts[2].'Sniff';
        if (class_exists($secondGuess)) {
            return $secondGuess;
        }

        $thirdGuess = 'PHP_CodeSniffer\\Standards\\'.$parts[0].'\\Sniffs\\'.$parts[1].'\\'.$parts[2].'Sniff';
        return $thirdGuess;
    }

    public static function guessSniffCodeBySniffClass(string $sniffClass) : string
    {
        $parts = explode('\\', $sniffClass);

        self::ensureSniffClassNameIsValid($sniffClass, $parts);

        $standardName = $parts[count($parts)-4];
        if (Strings::endsWith($standardName, 'CodingStandard')) {
            $standardName = substr($standardName, 0, -strlen('CodingStandard'));
        }

        $categoryName = $parts[count($parts)-2];

        $sniffName = $parts[count($parts)-1];
        $sniffName = substr($sniffName, 0, -strlen('Sniff'));

        return $standardName.'.'.$categoryName.'.'.$sniffName;
    }

    private static function ensureSniffClassNameIsValid(string $sniffClass, array $parts)
    {
        if (count($parts) < 4) {
            throw new \Exception(sprintf(
                '"%s" is not valid sniff class name. Name in form %s or %s is expected.',
                $sniffClass,
                '<Name>CodingStandard\Sniffs\<Category>\<Name>Sniff',
                '<Name>\CodingStandard\Sniffs\<Category>\<Name>Sniff'
            ));
        }
    }
}
