<?php

/*
 * This file is part of Symplify
 * Copyright (c) 2016 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\PHP7_CodeSniffer;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symplify\PHP7_CodeSniffer\Event\CheckFileTokenEvent;
use Symplify\PHP7_CodeSniffer\File\File;
use Symplify\PHP7_CodeSniffer\File\SourceFilesProvider;

final class Php7CodeSniffer
{
    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @var SourceFilesProvider
     */
    private $sourceFilesProvider;

    public function __construct(
        EventDispatcherInterface $eventDispatcher,
        SourceFilesProvider $sourceFilesProvider
    ) {
        $this->eventDispatcher = $eventDispatcher;
        $this->sourceFilesProvider = $sourceFilesProvider;
    }

    public function registerSniffs(array $standards, array $sniffs)
    {
    }

    public function runForSource(array $source, bool $isFixer = false)
    {
        $this->ensureSniffsAreRegistered();

        $files = $this->sourceFilesProvider->getFilesForSource($source, $isFixer);

        foreach ($files as $file) {
            if ($isFixer) {
                $this->processFileWithFixer($file);
            } else {
                $this->processFile($file);
            }

            $file->cleanUp();
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

    private function ensureSniffsAreRegistered()
    {
        // todo
    }
}
