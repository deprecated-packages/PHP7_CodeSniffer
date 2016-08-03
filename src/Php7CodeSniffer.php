<?php

/*
 * This file is part of Symplify
 * Copyright (c) 2016 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\PHP7_CodeSniffer;

use Symplify\PHP7_CodeSniffer\EventDispatcher\Event\CheckFileTokenEvent;
use Symplify\PHP7_CodeSniffer\EventDispatcher\SniffDispatcher;
use Symplify\PHP7_CodeSniffer\Exception\RuntimeException;
use Symplify\PHP7_CodeSniffer\File\File;
use Symplify\PHP7_CodeSniffer\File\Provider\FilesProvider;
use Symplify\PHP7_CodeSniffer\Sniff\SniffFactory;
use Symplify\PHP7_CodeSniffer\Sniff\SniffClassesResolver;

final class Php7CodeSniffer
{
    /**
     * @var SniffDispatcher
     */
    private $sniffDispatcher;

    /**
     * @var FilesProvider
     */
    private $filesProvider;

    /**
     * @var SniffClassesResolver
     */
    private $sniffClassesResolver;

    /**
     * @var SniffFactory
     */
    private $sniffFactory;

    public function __construct(
        SniffDispatcher $sniffDispatcher,
        FilesProvider $sourceFilesProvider,
        SniffClassesResolver $sniffProvider,
        SniffFactory $sniffFactory
    ) {
        $this->sniffDispatcher = $sniffDispatcher;
        $this->filesProvider = $sourceFilesProvider;
        $this->sniffClassesResolver = $sniffProvider;
        $this->sniffFactory = $sniffFactory;

        $this->setupRequirements();
    }

    public function registerSniffs(array $standards, array $sniffs)
    {
        $sniffClasses = $this->sniffClassesResolver->resolveFromStandardsAndSniffs(
            $standards,
            $sniffs
        );
        $sniffs = $this->sniffFactory->createFromSniffClassNames($sniffClasses);
        $this->sniffDispatcher->addSniffListeners($sniffs);
    }

    public function runForSource(array $source, bool $isFixer)
    {
        $this->ensureSniffsAreRegistered();

        $files = $this->filesProvider->getFilesForSource($source, $isFixer);

        foreach ($files as $file) {
            if ($isFixer) {
                $this->processFileWithFixer($file);
            } else {
                $this->processFile($file);
            }

            $file->cleanUp(); // todo: check performance influence
        }
    }

    private function processFile(File $file)
    {
        foreach ($file->getTokens() as $stackPointer => $token) {
            $this->sniffDispatcher->dispatch(
                $token['code'],
                new CheckFileTokenEvent($file, $stackPointer)
            );
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

    private function setupRequirements()
    {
        $this->ensureLineEndingsAreDetected();
        $this->setupVerbosityToMakeLegacyCodeRun();
    }

    /**
     * Ensure this option is enabled or else line endings will not always
     * be detected properly for files created on a Mac with the /r line ending.
     */
    private function ensureLineEndingsAreDetected()
    {
        ini_set('auto_detect_line_endings', true);
    }

    private function setupVerbosityToMakeLegacyCodeRun()
    {
        define('PHP_CODESNIFFER_VERBOSITY', 0);
    }

    private function ensureSniffsAreRegistered()
    {
        $listeners = $this->sniffDispatcher->getListeners();
        if ($listeners === []) {
            throw new RuntimeException(
                'You need to specify some sniffs with "--standards=..." or "--sniffs=...".'
            );
        }
    }
}
