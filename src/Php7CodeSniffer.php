<?php

/*
 * This file is part of Symplify
 * Copyright (c) 2016 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\PHP7_CodeSniffer;

use Symplify\PHP7_CodeSniffer\EventDispatcher\Event\CheckFileTokenEvent;
use Symplify\PHP7_CodeSniffer\EventDispatcher\SniffDispatcher;
use Symplify\PHP7_CodeSniffer\Exception\AnySniffMissingException;
use Symplify\PHP7_CodeSniffer\File\File;
use Symplify\PHP7_CodeSniffer\File\Provider\FilesProvider;
use Symplify\PHP7_CodeSniffer\Sniff\SniffFactory;

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
     * @var SniffFactory
     */
    private $sniffFactory;

    public function __construct(
        SniffDispatcher $sniffDispatcher,
        FilesProvider $sourceFilesProvider,
        SniffFactory $sniffFactory
    ) {
        $this->sniffDispatcher = $sniffDispatcher;
        $this->filesProvider = $sourceFilesProvider;
        $this->sniffFactory = $sniffFactory;

        $this->setupRequirements();
    }

    public function runCommand(Php7CodeSnifferCommand $command)
    {
        $this->registerSniffs(
            $command->getStandards(),
            $command->getSniffs(),
            $command->getExcludedSniffs()
        );

        $this->ensureSniffsAreRegistered();

        $this->runForSource($command->getSource(), $command->isFixer());
    }

    private function registerSniffs(array $standards, array $extraSniffs, array $excludedSniffs)
    {
        $sniffs = $this->sniffFactory->createFromStandardsAndSniffs(
            $standards,
            $extraSniffs,
            $excludedSniffs
        );

        $this->sniffDispatcher->addSniffListeners($sniffs);
    }

    private function runForSource(array $source, bool $isFixer)
    {
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
        if (!defined('PHP_CODESNIFFER_VERBOSITY')) {
            define('PHP_CODESNIFFER_VERBOSITY', 0);
        }
    }

    private function ensureSniffsAreRegistered()
    {
        $listeners = $this->sniffDispatcher->getListeners();
        if ($listeners === []) {
            throw new AnySniffMissingException(
                'You need to specify some sniffs with "--standards=..." or "--sniffs=...".'
            );
        }
    }
}
