<?php

/*
 * This file is part of Symplify
 * Copyright (c) 2016 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\PHP7_CodeSniffer\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symplify\PHP7_CodeSniffer\Console\ExitCode;
use Symplify\PHP7_CodeSniffer\Console\Style\CodeSnifferStyle;
use Symplify\PHP7_CodeSniffer\Php7CodeSniffer;
use Symplify\PHP7_CodeSniffer\Report\ErrorDataCollector;
use Throwable;

final class RunCommand extends Command
{
    /**
     * @var CodeSnifferStyle
     */
    private $codeSnifferStyle;

    /**
     * @var ErrorDataCollector
     */
    private $reportCollector;

    /**
     * @var Php7CodeSniffer
     */
    private $php7CodeSniffer;

    public function __construct(
        CodeSnifferStyle $codeSnifferStyle,
        ErrorDataCollector $reportCollector,
        Php7CodeSniffer $php7CodeSniffer
    ) {
        $this->codeSnifferStyle = $codeSnifferStyle;
        $this->reportCollector = $reportCollector;
        $this->php7CodeSniffer = $php7CodeSniffer;

        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('run');
        $this->setDescription('Checks code against coding standard.');

        $this->addArgument(
            'source',
            InputArgument::REQUIRED | InputArgument::IS_ARRAY,
            'Files or directories to process.'
        );
        $this->addOption('fix', null, null, 'Fix all fixable errors.');

        $this->addOption(
            'standards',
            null,
            InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY,
            'List of coding standards to use.'
        );
        $this->addOption(
            'sniffs',
            null,
            InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY,
            'List of sniff codes to use.'
        );
        $this->addOption(
            'exclude-sniffs',
            null,
            InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY,
            'List of sniff codes to be excluded.'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $this->php7CodeSniffer->registerSniffs(
                $input->getOption('standards'),
                $input->getOption('sniffs'),
                $input->getOption('exclude-sniffs')
            );

            $this->php7CodeSniffer->runForSource(
                $input->getArgument('source'),
                $input->getOption('fix')
            );

            // 2. print found errors to the output
            if ($this->reportCollector->getErrorCount()) {
                if ($input->getOption('fix')) {
                    $this->printUnfixedErrors();
                } else {
                    $this->printErrors();
                    $this->printFixingNote();
                }

                return ExitCode::ERROR;
            }

            $this->codeSnifferStyle->success(
                'Great job! Your code is completely fine. Take a break and look around you.'
            );

            return ExitCode::SUCCESS;
        } catch (Throwable $throwable) {
            $this->codeSnifferStyle->error($throwable->getMessage());

            return ExitCode::ERROR;
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
                'Good news is, we can fix %s of them for you. Just add "--fix".',
                $howMany
            ));
        }
    }

    private function printUnfixedErrors()
    {
        $this->codeSnifferStyle->writeErrorReports(
            $this->reportCollector->getUnfixableErrorMessages()
        );

        if ($this->reportCollector->getFixableErrorCount()) {
            $this->codeSnifferStyle->success(sprintf(
                'Congrats! %d errors were fixed.',
                $this->reportCollector->getFixableErrorCount()
            ));
        }

        if ($this->reportCollector->getUnfixableErrorCount()) {
            $this->codeSnifferStyle->error(sprintf(
                '%d errors could not be fixed. You have to do it manually.',
                $this->reportCollector->getUnfixableErrorCount()
            ));
        }
    }
}
