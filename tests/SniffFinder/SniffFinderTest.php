<?php

namespace Symplify\PHP7_CodeSniffer\SniffFinder\Tests;

use PHPUnit\Framework\TestCase;
use Symplify\PHP7_CodeSniffer\SniffFinder\SniffClassFilter;
use Symplify\PHP7_CodeSniffer\SniffFinder\SniffClassRobotLoaderFactory;
use Symplify\PHP7_CodeSniffer\SniffFinder\SniffFinder;
use Symplify\PHP7_CodeSniffer\Standard\StandardFinder;

final class SniffFinderTest extends TestCase
{
    /**
     * @var SniffFinder
     */
    private $sniffFinder;

    /**
     * @var StandardFinder
     */
    private $standardFinder;

    protected function setUp()
    {
        $this->sniffFinder = new SniffFinder(
            new SniffClassRobotLoaderFactory(),
            new SniffClassFilter()
        );
        $this->standardFinder = new StandardFinder();
    }

    public function testFindAllSniffs()
    {
        $allSniffs = $this->sniffFinder->findAllSniffClasses();
        $this->assertGreaterThan(250, $allSniffs);
    }

    public function testFindSniffsInDirectory()
    {
        $psr2RulesetPath = $this->standardFinder->getRulesetPathForStandardName('PSR2');

        $sniffs = $this->sniffFinder->findAllSniffClassesInDirectory(
            dirname($psr2RulesetPath)
        );
        $this->assertCount(12, $sniffs);
    }
}
