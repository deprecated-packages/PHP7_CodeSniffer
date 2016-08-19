<?php

/*
 * This file is part of Symplify
 * Copyright (c) 2016 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\PHP7_CodeSniffer\Application;

use Symplify\PHP7_CodeSniffer\Application\Command\RunApplicationCommand;
use Symplify\PHP7_CodeSniffer\Configuration\ConfigurationResolver;
use Symplify\PHP7_CodeSniffer\EventDispatcher\SniffDispatcher;
use Symplify\PHP7_CodeSniffer\Exception\AnySniffMissingException;
use Symplify\PHP7_CodeSniffer\File\Provider\FilesProvider;
use Symplify\PHP7_CodeSniffer\Legacy\LegacyConfiguration;
use Symplify\PHP7_CodeSniffer\Sniff\SniffSetFactory;
use Symplify\PHP7_CodeSniffer\Sniff\Xml\DataCollector\ExcludedSniffDataCollector;

final class Application
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
     * @var SniffSetFactory
     */
    private $sniffSetFactory;

    /**
     * @var ExcludedSniffDataCollector
     */
    private $excludedSniffDataCollector;

    /**
     * @var ConfigurationResolver
     */
    private $configurationResolver;

    /**
     * @var FileProcessor
     */
    private $fileProcessor;

    public function __construct(
        SniffDispatcher $sniffDispatcher,
        FilesProvider $sourceFilesProvider,
        SniffSetFactory $sniffFactory,
        ExcludedSniffDataCollector $excludedSniffDataCollector,
        ConfigurationResolver $configurationResolver,
        FileProcessor $fileProcessor
    ) {
        $this->sniffDispatcher = $sniffDispatcher;
        $this->filesProvider = $sourceFilesProvider;
        $this->sniffSetFactory = $sniffFactory;
        $this->excludedSniffDataCollector = $excludedSniffDataCollector;
        $this->configurationResolver = $configurationResolver;
        $this->fileProcessor = $fileProcessor;

        LegacyConfiguration::setup();
    }

    public function runCommand(RunApplicationCommand $command)
    {
        $command = $this->resolveCommandConfiguration($command);

        $this->excludedSniffDataCollector->addExcludedSniffs($command->getExcludedSniffs());

        $this->createAndRegisterSniffsToSniffDispatcher(
            $command->getStandards(),
            $command->getSniffs()
        );

        $this->runForSource($command->getSource(), $command->isFixer());
    }

    private function createAndRegisterSniffsToSniffDispatcher(array $standards, array $extraSniffs)
    {
        $sniffs = $this->sniffSetFactory->createFromStandardsAndSniffs($standards, $extraSniffs);
        $this->ensureAtLeastOneSniffIsRegistered($sniffs);
        $this->sniffDispatcher->addSniffListeners($sniffs);
    }

    private function runForSource(array $source, bool $isFixer)
    {
        $files = $this->filesProvider->getFilesForSource($source, $isFixer);
        $this->fileProcessor->processFiles($files, $isFixer);
    }

    private function ensureAtLeastOneSniffIsRegistered(array $sniffs)
    {
        if (count($sniffs) < 1) {
            throw new AnySniffMissingException(
                'You need to specify some sniffs with "--standards=..." or "--sniffs=...".'
            );
        }
    }

    private function resolveCommandConfiguration(
        RunApplicationCommand $command
    ) : RunApplicationCommand {
        return new RunApplicationCommand(
            $command->getSource(),
            $this->configurationResolver->resolve('standards', $command->getStandards()),
            $this->configurationResolver->resolve('sniffs', $command->getSniffs()),
            $this->configurationResolver->resolve('sniffs', $command->getExcludedSniffs()),
            $command->isFixer()
        );
    }
}
