<?php

namespace Symplify\PHP7_CodeSniffer\Tests;

use PHPUnit\Framework\TestCase;
use Symplify\PHP7_CodeSniffer\Php7CodeSniffer;
use Symplify\PHP7_CodeSniffer\Php7CodeSnifferCommand;

final class Php7CodeSnifferTest extends TestCase
{
    /**
     * @var Php7CodeSniffer
     */
    private $php7CodeSniffer;

    protected function setUp()
    {
        $this->php7CodeSniffer = Instantiator::createPhp7CodeSniffer();
    }

    /**
     * @expectedException \Symplify\PHP7_CodeSniffer\Exception\AnySniffMissingException
     */
    public function testRunWithoutSniffs()
    {
        $this->php7CodeSniffer->runCommand($this->createCommand([]));
    }

    private function createCommand(array $standards) : Php7CodeSnifferCommand
    {
        return new Php7CodeSnifferCommand(
            $source = [__DIR__ . '/Php7CodeSnifferSource'],
            $standards,
            $sniffs = [],
            $excludedSniffs = [],
            $isFixer = true
        );
    }
}
