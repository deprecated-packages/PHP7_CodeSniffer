<?php

namespace Symplify\PHP7_CodeSniffer\Tests\Sniff\Finder;

use PHP_CodeSniffer\Util\Tokens;
use PHPUnit\Framework\TestCase;
use Symplify\PHP7_CodeSniffer\Sniff\Finder\SniffClassFilter;
use Symplify\PHP7_CodeSniffer\Sniff\Finder\SniffClassRobotLoaderFactory;
use Symplify\PHP7_CodeSniffer\Sniff\Finder\SniffFinder;
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
        new Tokens();

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
