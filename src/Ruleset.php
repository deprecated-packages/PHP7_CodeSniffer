<?php

/*
 * This file is part of Symplify
 * Copyright (c) 2016 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\PHP7_CodeSniffer;

use PHP_CodeSniffer\Sniffs\Sniff;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symplify\PHP7_CodeSniffer\Event\CheckFileTokenEvent;
use Symplify\PHP7_CodeSniffer\Ruleset\RulesetBuilder;
use Symplify\PHP7_CodeSniffer\SniffFinder\SniffProvider;

final class Ruleset
{
    /**
     * The key is the sniff code
     * and the value is the sniff object.
     *
     * @var Sniff[]
     */
    private $sniffs = [];

    /**
     * An array of rules from the ruleset.xml file.
     *
     * It may be empty, indicating that the ruleset does not override
     * any of the default sniff settings.
     *
     * @var array<string, mixed>
     */
    public $ruleset = [];

    /**
     * @var SniffProvider
     */
    private $sniffProvider;

    /**
     * @var RulesetBuilder
     */
    private $rulesetBuilder;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    public function __construct(
        SniffProvider $sniffProvider,
        RulesetBuilder $rulesetBuilder,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->sniffProvider = $sniffProvider;
        $this->rulesetBuilder = $rulesetBuilder;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function createSniffList()
    {
        $this->registerSniffs($this->sniffProvider->getActiveSniffs());
        $this->loadSniffsToTokensTheyListenToo();
    }

    private function registerSniffs(array $sniffClasses)
    {
        // these classes should be registered a services
        // and collected by event dispatcher, as they subscribe
        // to tokens...
        foreach ($sniffClasses as $sniffCode => $sniffClass) {
            $this->sniffs[$sniffCode] = new $sniffClass;
        }
    }

    private function loadSniffsToTokensTheyListenToo()
    {
        $this->ruleset = $this->rulesetBuilder->getRuleset();

        foreach ($this->sniffs as $sniffCode => $sniffObject) {
            $this->setCustomProperties($sniffCode);

            $tokens = $this->sniffs[$sniffCode]->register();
            foreach ($tokens as $token) {
                $this->eventDispatcher->addListener($token, function (CheckFileTokenEvent $checkFileToken) use ($sniffObject) {
                    $sniffObject->process($checkFileToken->getFile(), $checkFileToken->getStackPointer());
                });
            }
        }
    }

    private function setCustomProperties(string $sniffCode)
    {
        if (isset($this->ruleset[$sniffCode]['properties']) === true) {
            foreach ($this->ruleset[$sniffCode]['properties'] as $name => $value) {
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
        if (isset($this->sniffs[$sniffCode]) === false) {
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

        $this->sniffs[$sniffCode]->$name = $value;
    }
}
