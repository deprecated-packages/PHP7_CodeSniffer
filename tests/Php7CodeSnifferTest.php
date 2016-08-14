<?php

namespace Symplify\PHP7_CodeSniffer\Tests;

use PHPUnit\Framework\TestCase;
use Symplify\PHP7_CodeSniffer\Application\Php7CodeSnifferApplication;
use Symplify\PHP7_CodeSniffer\Application\RunApplicationCommand;

final class Php7CodeSnifferTest extends TestCase
{
    /**
     * @var Php7CodeSnifferApplication
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

    private function createCommand(array $standards) : RunApplicationCommand
    {
        return new RunApplicationCommand(
            $source = [__DIR__ . '/Php7CodeSnifferSource'],
            $standards,
            $sniffs = [],
            $excludedSniffs = [],
            $isFixer = true
        );
    }
}
