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
use Symplify\PHP7_CodeSniffer\Console\Output\InfoMessagePrinter;
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
    private $errorDataCollector;

    /**
     * @var Php7CodeSniffer
     */
    private $php7CodeSniffer;

    /**
     * @var InfoMessagePrinter
     */
    private $infoMessagePrinter;

    public function __construct(
        CodeSnifferStyle $codeSnifferStyle,
        ErrorDataCollector $reportCollector,
        Php7CodeSniffer $php7CodeSniffer,
        InfoMessagePrinter $infoMessagePrinter
    ) {
        $this->codeSnifferStyle = $codeSnifferStyle;
        $this->errorDataCollector = $reportCollector;
        $this->php7CodeSniffer = $php7CodeSniffer;
        $this->infoMessagePrinter = $infoMessagePrinter;

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

            if ($this->errorDataCollector->getErrorCount()) {
                $this->infoMessagePrinter->printFoundErrorsStatus($input->getOption('fix'));

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
}
