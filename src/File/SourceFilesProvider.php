<?php

/*
 * This file is part of Symplify
 * Copyright (c) 2016 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\PHP7_CodeSniffer\File;

use SplFileInfo;
use Symplify\PHP7_CodeSniffer\Configuration\Configuration;
use Symplify\PHP7_CodeSniffer\Finder\SourceFinder;
use Symplify\PHP7_CodeSniffer\Ruleset;

final class SourceFilesProvider
{
    /**
     * @var File[]
     */
    private $files;

    /**
     * @var SourceFinder
     */
    private $sourceFinder;

    /**
     * @var FileFactory
     */
    private $fileFactory;

    /**
     * @var Configuration
     */
    private $configuration;

    public function __construct(
        SourceFinder $sourceFinder,
        FileFactory $fileFactory,
        Configuration $configuration
    ) {
        $this->sourceFinder = $sourceFinder;
        $this->fileFactory = $fileFactory;
        $this->configuration = $configuration;
    }

    /**
     * @return File[]
     */
    public function getFiles() : array
    {
        if ($this->files) {
            return $this->files;
        }

        $files = $this->sourceFinder->find($this->configuration->getSource());
        $this->files = $this->wrapFilesToValueObjects($files);

        return $this->files;
    }

    /**
     * @param SplFileInfo[] $files
     * @return File[]
     */
    private function wrapFilesToValueObjects(array $files) : array
    {
        foreach ($files as $name => $fileInfo) {
            $files[$name] = $this->fileFactory->create($name);
        }

        return $files;
    }
}
