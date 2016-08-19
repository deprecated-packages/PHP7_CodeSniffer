<?php

namespace Symplify\PHP7_CodeSniffer\Tests\Application;

use PHPUnit\Framework\TestCase;
use Symplify\PHP7_CodeSniffer\Application\Application;
use Symplify\PHP7_CodeSniffer\Application\Command\RunApplicationCommand;
use Symplify\PHP7_CodeSniffer\Tests\Instantiator;

final class Php7CodeSnifferTest extends TestCase
{
    /**
     * @var Application
     */
    private $application;

    protected function setUp()
    {
        $this->application = Instantiator::createApplication();
    }

    /**
     * @expectedException \Symplify\PHP7_CodeSniffer\Exception\AnySniffMissingException
     */
    public function testRunWithoutSniffs()
    {
        $this->application->runCommand($this->createCommand([]));
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
