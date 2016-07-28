<?php

/*
 * This file is part of Symplify
 * Copyright (c) 2016 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\PHP7_CodeSniffer\Configuration;

final class Configuration
{
    /**
     * @var array
     */
    private $standards = [];

    /**
     * @var string
     */
    private $reportClass;

    /**
     * @var ConfigurationResolver
     */
    private $configurationResolver;

    /**
     * @var string[]
     */
    private $sniffs = [];

    /**
     * @var array
     */
    private $source = [];

    /**
     * @var bool
     */
    private $isFixer;

    public function __construct(ConfigurationResolver $configurationResolver)
    {
        $this->configurationResolver = $configurationResolver;
    }

    public function resolveFromArray(array $options)
    {
        $options = $this->configurationResolver->resolve($options);

        $this->standards = $options['standards'];
        $this->sniffs = $options['sniffs'];
        $this->source = $options['source'];
        $this->isFixer = $options['fix'];
    }

    public function getStandards() : array
    {
        return $this->standards;
    }

    public function getSniff() : array
    {
        return $this->sniffs;
    }

    public function getReportClass() : string
    {
        return $this->reportClass;
    }

    public function getSource() : array
    {
        return $this->source;
    }

    public function isFixer() : bool
    {
        return $this->isFixer;
    }
}
