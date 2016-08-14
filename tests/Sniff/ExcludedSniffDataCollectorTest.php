<?php

namespace Symplify\PHP7_CodeSniffer\Tests\Sniff;

use PHPUnit\Framework\TestCase;
use Symplify\PHP7_CodeSniffer\Sniff\ExcludedSniffDataCollector;

final class ExcludedSniffDataCollectorTest extends TestCase
{
    public function testIsSniffClassExcluded()
    {
        $excludedSniffDataCollector = new ExcludedSniffDataCollector();

        $excludedSniffDataCollector->addExcludedSniff('Standard.Category.Name');
        $excludedSniffDataCollector->addExcludedSniffs(['AnotherStandard.Category.Name']);

        $this->assertTrue(
            $excludedSniffDataCollector->isSniffClassExcluded('Standard.Category.Name')
        );
        $this->assertFalse(
            $excludedSniffDataCollector->isSniffClassExcluded('Standard.Category.NonexistingName')
        );
    }
}
