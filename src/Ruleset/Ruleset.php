<?php

/*
 * This file is part of Symplify
 * Copyright (c) 2016 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\PHP7_CodeSniffer\Ruleset;

use PHP_CodeSniffer\Sniffs\Sniff;

final class Ruleset
{
    /**
     * @var RulesetBuilder
     */
    private $rulesetBuilder;

    public function __construct(RulesetBuilder $rulesetBuilder)
    {
        $this->rulesetBuilder = $rulesetBuilder;
    }

    /**
     * @param array|Sniff[] $sniffs
     */
    public function decorateSniffsWithCustomRules(array $sniffs)
    {
        // todo: put to SniffDispatcher on sniff loading?
        $ruleset = $this->rulesetBuilder->getRuleset();

        foreach ($sniffs as $sniffCode => $sniffObject) {
            if (!isset($ruleset[$sniffCode]['properties'])) {
                continue;
            }

            foreach ($ruleset[$sniffCode]['properties'] as $name => $value) {
                $this->setSniffProperty($sniffCode, $name, $value);
            }
        }
    }

    /**
     * @param string $sniffCode
     * @param string $name
     * @param string|array $value
     */
    private function setSniffProperty(string $sniffCode, string $name, $value)
    {
        if (isset($sniffs[$sniffCode]) === false) {
            return;
        }

        $name = trim($name);
        if (is_string($value)) {
            $value = trim($value);
        }

        // Special case for booleans.
        if ($value === 'true') {
            $value = true;
        } elseif ($value === 'false') {
            $value = false;
        }

        $sniffs[$sniffCode]->$name = $value;
    }
}
