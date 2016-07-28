<?php

/*
 * This file is part of Symplify
 * Copyright (c) 2016 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\PHP7_CodeSniffer\EventDispatcher;

use Closure;
use ReflectionFunction;
use SplObjectStorage;
use Symplify\PHP7_CodeSniffer\SniffFinder\Naming\SniffNaming;

final class CurrentListenerSniffCodeProvider
{
    /**
     * @var mixed
     */
    private $currentListener;

    /**
     * @var string[]
     */
    private $sniffClassToSniffCodeMap = [];

    public function __construct()
    {
        $this->sniffClassToSniffCodeMap = new SplObjectStorage();
    }

    /**
     * @param array|object $currentListener
     */
    public function setCurrentListener($currentListener)
    {
        $this->currentListener = $currentListener;
    }

    public function getCurrentListenerSniffCode() : string
    {
        if (isset($this->sniffClassToSniffCodeMap[$this->currentListener])) {
            return $this->sniffClassToSniffCodeMap[$this->currentListener];
        }

        $closureReflection = new ReflectionFunction($this->currentListener);
        $sniffClass = get_class($closureReflection->getStaticVariables()['sniffObject']);
        $sniffCode = SniffNaming::guessSniffCodeBySniffClass($sniffClass);

        $this->sniffClassToSniffCodeMap[$this->currentListener] = $sniffCode;

        return $sniffCode;
    }
}
