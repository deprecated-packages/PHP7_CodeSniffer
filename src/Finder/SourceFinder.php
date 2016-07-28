<?php

/*
 * This file is part of Symplify
 * Copyright (c) 2016 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\PHP7_CodeSniffer\Finder;

use SplFileInfo;
use Symfony\Component\Finder\Finder;

final class SourceFinder
{
    /**
     * @return SplFileInfo[]
     */
    public function find(array $source) : array
    {
        $files = [];

        foreach ($source as $singleSource) {
            if (is_file($singleSource)) {
                $files[$singleSource] = new SplFileInfo($singleSource);
            } else {
                $finder = (new Finder())->files()
                    ->name('*.php')
                    ->in($singleSource);

                $files = array_merge(
                    $files,
                    iterator_to_array($finder->getIterator())
                );
            }
        }

        return $files;
    }
}
