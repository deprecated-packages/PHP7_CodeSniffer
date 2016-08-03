<?php

/*
 * This file is part of Symplify
 * Copyright (c) 2016 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\PHP7_CodeSniffer\Configuration;

use Symplify\PHP7_CodeSniffer\Contract\Configuration\OptionResolver\OptionResolverInterface;

final class ConfigurationResolver
{
    /**
     * @var OptionResolverInterface[]
     */
    private $optionResolvers = [];

    public function addOptionResolver(OptionResolverInterface $optionResolver)
    {
        $this->optionResolvers[$optionResolver->getName()] = $optionResolver;
    }

    public function resolve(string $name, array $source) : array
    {
        return $this->optionResolvers[$name]->resolve($source);
    }
}
