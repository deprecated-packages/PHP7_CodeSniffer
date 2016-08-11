<?php

/*
 * This file is part of Symplify
 * Copyright (c) 2016 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\PHP7_CodeSniffer\Ruleset\ToSniffNormalizer;

use Symplify\PHP7_CodeSniffer\Contract\Ruleset\ToSniffNormalizer\ToSniffNormalizerInterface;
use Symplify\PHP7_CodeSniffer\Ruleset\Routing\Router;

final class SniffCodeToSniffNormalizer implements ToSniffNormalizerInterface
{
    /**
     * @var Router
     */
    private $router;

    public function __construct(Router $router)
    {

        $this->router = $router;
    }

    public function isMatch(string $reference) : bool
    {
        $parts = explode('.', $reference);
        if (count($parts) >= 3) {
            return true;
        }

        return false;
    }

    public function normalize(string $reference) : array
    {
        return [
            $reference => $this->router->getClassFromSniffCode($reference)
        ];
    }
}
