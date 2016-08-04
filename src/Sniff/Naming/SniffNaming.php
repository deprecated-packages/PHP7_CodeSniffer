<?php

/*
 * This file is part of Symplify
 * Copyright (c) 2016 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\PHP7_CodeSniffer\Sniff\Naming;

use Nette\Utils\Strings;
use Symplify\PHP7_CodeSniffer\Exception\Sniff\Naming\InvalidSniffClassException;
use Symplify\PHP7_CodeSniffer\Exception\Sniff\Naming\InvalidSniffCodeException;
use Symplify\PHP7_CodeSniffer\Exception\Sniff\Naming\SniffClassCouldNotBeFoundException;

final class SniffNaming
{
    public static function guessSniffClassBySniffCode(string $sniffCode) : string
    {
        self::ensureSniffCodeIsValid($sniffCode);

        $parts = explode('.', $sniffCode);

        $firstGuess = $parts[0].'CodingStandard\\Sniffs\\'.$parts[1].'\\'.$parts[2].'Sniff';
        if (class_exists($firstGuess)) {
            return $firstGuess;
        }

        $secondGuess = $parts[0].'\\CodingStandard\\Sniffs\\'.$parts[1].'\\'.$parts[2].'Sniff';
        if (class_exists($secondGuess)) {
            return $secondGuess;
        }

        $thirdGuess = 'PHP_CodeSniffer\\Standards\\'.$parts[0]
            .'\\Sniffs\\'.$parts[1].'\\'.$parts[2].'Sniff';

        if (class_exists($thirdGuess)) {
            return $thirdGuess;
        }

        self::reportClassCouldNotBeFound($sniffCode, [$firstGuess, $secondGuess, $thirdGuess]);
    }

    public static function guessSniffCodeBySniffClass(string $sniffClass) : string
    {
        self::ensureSniffClassNameIsValid($sniffClass);

        $parts = explode('\\', $sniffClass);

        $standardName = $parts[count($parts)-4];
        if (Strings::endsWith($standardName, 'CodingStandard')) {
            $standardName = substr($standardName, 0, -strlen('CodingStandard'));
        }

        $categoryName = $parts[count($parts)-2];

        $sniffName = $parts[count($parts)-1];
        $sniffName = substr($sniffName, 0, -strlen('Sniff'));

        return $standardName.'.'.$categoryName.'.'.$sniffName;
    }

    private static function ensureSniffCodeIsValid(string $sniffCode)
    {
        $parts = explode('.', $sniffCode);
        
        if (count($parts) !== 3) {
            throw new InvalidSniffCodeException(
                sprintf(
                    '"%s" is not valid sniff code. Code in form "%s" is expected.',
                    $sniffCode,
                    'Standard.Category.Specific'
                )
            );
        }
    }

    private static function ensureSniffClassNameIsValid(string $sniffClass)
    {
        $parts = explode('\\', $sniffClass);

        if (count($parts) < 4) {
            throw new InvalidSniffClassException(sprintf(
                '"%s" is not valid sniff class name. Name in form "%s" or "%s" is expected.',
                $sniffClass,
                '<Name>CodingStandard\Sniffs\<Category>\<Name>Sniff',
                '<Name>\CodingStandard\Sniffs\<Category>\<Name>Sniff'
            ));
        }
    }

    private static function reportClassCouldNotBeFound(string $sniffCode, array $guessedClasses)
    {
        throw new SniffClassCouldNotBeFoundException(sprintf(
            'Sniff class for code "%s" could not be found. We tried these options:'.PHP_EOL.' - %s ',
            $sniffCode,
            implode(PHP_EOL . ' - ', $guessedClasses)
        ));
    }
}
