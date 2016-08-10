<?php

namespace Symplify\PHP7_CodeSniffer\Tests\Console\Style;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symplify\PHP7_CodeSniffer\Console\Style\CodeSnifferStyle;

final class CodeSnifferStyleTest extends TestCase
{
    /**
     * @var BufferedOutput
     */
    private $consoleOutput;

    /**
     * @var CodeSnifferStyle
     */
    private $codeSnifferStyle;

    protected function setUp()
    {
        $this->consoleOutput = new BufferedOutput();
        $this->codeSnifferStyle = new CodeSnifferStyle(new ArgvInput(), $this->consoleOutput);
    }

    public function test()
    {
        // $this->codeSnifferStyle->error(); // todo:
    }
}