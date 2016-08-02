<?php

/*
 * This file is part of Symplify
 * Copyright (c) 2016 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\PHP7_CodeSniffer\Console\Command;

use Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symplify\PHP7_CodeSniffer\Console\Style\CodeSnifferStyle;
use Symplify\PHP7_CodeSniffer\ErrorDataCollector;
use Symplify\PHP7_CodeSniffer\File\SourceFilesProvider;
use Symplify\PHP7_CodeSniffer\Php7CodeSniffer;
use Symplify\PHP7_CodeSniffer\Ruleset;

final class CheckCommand extends Command
{
    /**
     * @var SourceFilesProvider
     */
    private $sourceFilesProvider;

    /**
     * @var CodeSnifferStyle
     */
    private $codeSnifferStyle;

    /**
     * @var ErrorDataCollector
     */
    private $reportCollector;

    /**
     * @var Ruleset
     */
    private $ruleset;

    /**
     * @var Php7CodeSniffer
     */
    private $php7CodeSniffer;

    public function __construct(
        SourceFilesProvider $sourceFilesProvider,
        CodeSnifferStyle $codeSnifferStyle,
        ErrorDataCollector $reportCollector,
        Ruleset $ruleset,
        Php7CodeSniffer $php7CodeSniffer
    ) {
        $this->sourceFilesProvider = $sourceFilesProvider;
        $this->codeSnifferStyle = $codeSnifferStyle;
        $this->reportCollector = $reportCollector;
        $this->ruleset = $ruleset;
        $this->php7CodeSniffer = $php7CodeSniffer;

        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('check');
        $this->setDescription('Checks code against coding standard.');

        $this->addArgument('source', InputArgument::REQUIRED | InputArgument::IS_ARRAY, 'Files or directories to process.');
        $this->addOption('fix', null, null, 'Fix all fixable errors.');

        $this->addOption('standards', null, InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'List of coding standards to use.');
        $this->addOption('sniffs', null, InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'List of sniff codes to use.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $this->ruleset->createSniffList();

            $this->php7CodeSniffer->registerSniffs($input->getOption('standards'), $input->getOption('sniffs'));
            $this->php7CodeSniffer->runForSource($input->getArgument('source'), $input->getOption('fix'));

            // 3. finish it
//            $this->codeSnifferStyle->newLine();

            // 2. print found errors to the output
            if ($this->reportCollector->getErrorCount()) {
                $this->printErrors();
                $this->printFixingNote();

                return 1;
            }

            $this->codeSnifferStyle->success('Great job! Your code is completely fine.');

            return 0;
        } catch (Exception $exception) {
            $this->codeSnifferStyle->error($exception->getMessage());

            return 0;
        }
    }

    private function printErrors()
    {
        $this->codeSnifferStyle->writeErrorReports($this->reportCollector->getErrorMessages());
        $this->codeSnifferStyle->error(sprintf(
            '%d errors were found.',
            $this->reportCollector->getErrorCount()
        ));
    }

    private function printFixingNote()
    {
        if ($fixableCount = $this->reportCollector->getFixableErrorCount()) {
            $howMany = $fixableCount;
            if ($fixableCount === $this->reportCollector->getErrorCount()) {
                $howMany = 'all';
            }

            $this->codeSnifferStyle->success(sprintf(
                'We can fix %s of them automatically.',
                $howMany
            ));
        }
    }
}
