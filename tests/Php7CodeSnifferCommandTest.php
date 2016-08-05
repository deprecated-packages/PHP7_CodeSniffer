<?php

namespace Symplify\PHP7_CodeSniffer\Tests;

use PHPUnit\Framework\TestCase;
use Symplify\PHP7_CodeSniffer\Php7CodeSnifferCommand;

final class Php7CodeSnifferCommandTest extends TestCase
{
    public function testConstructor()
    {
        $command = new Php7CodeSnifferCommand(
            $source = ['source'],
            $standards = ['standards'],
            $sniffs = ['sniffs'],
            $excludedSniffs = ['excluded-sniffs'],
            $isFixer = true
        );

        $this->assertSame($excludedSniffs, $command->getExcludedSniffs());
        $this->assertSame($source, $command->getSource());
        $this->assertSame($standards, $command->getStandards());
        $this->assertSame($sniffs, $command->getSniffs());
        $this->assertSame($excludedSniffs, $command->getExcludedSniffs());
        $this->assertSame($isFixer, $command->isFixer());
    }
}
