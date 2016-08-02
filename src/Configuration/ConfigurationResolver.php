<?php

/*
 * This file is part of Symplify
 * Copyright (c) 2016 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\PHP7_CodeSniffer\Configuration;

use Symfony\Component\OptionsResolver\OptionsResolver;

final class ConfigurationResolver
{
    /**
     * @var OptionsResolver
     */
    private $optionsResolver;

    public function __construct(OptionsResolver $optionsResolver)
    {
        $this->optionsResolver = $optionsResolver;
    }

    public function resolveSource(array $source) : array
    {
        return $this->resolveSingleOption($source, 'source');
    }

    public function resolveStandards(array $standards) : array
    {
        return $this->resolveSingleOption($standards, 'standards');
    }

    public function resolveSniffs(array $sniffs) : array
    {
        return $this->resolveSingleOption($sniffs, 'sniffs');
    }

    private function resolveSingleOption(array $value, string $optionName) : array
    {
        $options = $this->optionsResolver->resolve([
            $optionName => $value
        ]);

        return $options[$optionName];
    }
}
