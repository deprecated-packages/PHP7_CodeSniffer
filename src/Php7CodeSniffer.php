<?php

/*
 * This file is part of Symplify
 * Copyright (c) 2016 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\PHP7_CodeSniffer;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symplify\PHP7_CodeSniffer\Configuration\Configuration;
use Symplify\PHP7_CodeSniffer\Console\Progress\ShowProgress;
use Symplify\PHP7_CodeSniffer\Event\CheckFileTokenEvent;
use Symplify\PHP7_CodeSniffer\File\File;

final class Php7CodeSniffer
{
    /**
     * @var string
     */
    const VERSION = '4.0.0';

    /**
     * @var Configuration
     */
    private $configuration;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @var ShowProgress
     */
    private $showProgress;

    public function __construct(
        Configuration $configuration,
        EventDispatcherInterface $eventDispatcher,
        ShowProgress $showProgress
    ) {
        $this->configuration = $configuration;
        $this->eventDispatcher = $eventDispatcher;
        $this->showProgress = $showProgress;
    }

    public function runForFiles(array $files)
    {
        foreach ($files as $file) {
            if ($this->configuration->isFixer()) {
                $this->processFileWithFixer($file);
            } else {
                $this->processFile($file);
            }

            $file->cleanUp();
            $this->showProgress->advance();
        }
    }

    private function processFile(File $file)
    {
        foreach ($file->getTokens() as $stackPointer => $token) {
            $this->eventDispatcher->dispatch($token['code'], new CheckFileTokenEvent($file, $stackPointer));
        }
    }

    private function processFileWithFixer(File $file)
    {
        // 1. puts tokens into fixer
        $file->fixer->startFile($file);

        // 2. run all Sniff fixers
        $this->processFile($file);

        // 3. load changes to tokens
        $file->fixer->endChangeset();

        // 4. content has changed, save it!
        $newContent = $file->fixer->getContents();

        file_put_contents($file->getFilename(), $newContent);
    }
}
