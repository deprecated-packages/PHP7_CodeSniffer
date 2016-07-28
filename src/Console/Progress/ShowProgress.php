<?php

/*
 * This file is part of Symplify
 * Copyright (c) 2016 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\PHP7_CodeSniffer\Console\Progress;

use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\OutputInterface;

final class ShowProgress
{
    /**
     * @var OutputInterface
     */
    private $output;

    /**
     * @var ProgressBar
     */
    private $progressBar;

    public function __construct(OutputInterface $output)
    {
        $this->output = $output;
    }

    public function init(int $stepCount)
    {
        $this->progressBar = new ProgressBar($this->output, $stepCount);
    }

    public function advance()
    {
        $this->progressBar->advance();
    }

    public function finish()
    {
        $this->progressBar->finish();
    }
}
