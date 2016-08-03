<?php

namespace Symplify\PHP7_CodeSniffer\Ruleset\Tests\Rule;

use PHP_CodeSniffer\Standards\Squiz\Sniffs\PHP\EvalSniff;
use PHP_CodeSniffer\Util\Tokens;
use PHPUnit\Framework\TestCase;
use Symplify\PHP7_CodeSniffer\Ruleset\Routing\Router;
use Symplify\PHP7_CodeSniffer\Ruleset\Rule\ReferenceNormalizer;
use Symplify\PHP7_CodeSniffer\Sniff\Finder\SniffClassFilter;
use Symplify\PHP7_CodeSniffer\Sniff\Finder\SniffClassRobotLoaderFactory;
use Symplify\PHP7_CodeSniffer\Sniff\Finder\SniffFinder;
use Symplify\PHP7_CodeSniffer\Standard\StandardFinder;

final class ReferenceNormalizerTest extends TestCase
{
    /**
     * @var ReferenceNormalizer
     */
    private $referenceNormalizer;

    protected function setUp()
    {
        new Tokens();

        $this->referenceNormalizer = new ReferenceNormalizer(
            $this->createSniffFinder(),
            new StandardFinder(),
            new Router($this->createSniffFinder())
        );
    }

    public function testIsStandardReference()
    {
        $this->assertTrue($this->referenceNormalizer->isStandardReference('PSR1'));
        $this->assertFalse($this->referenceNormalizer->isStandardReference('non-existing'));
    }

    private function createSniffFinder() : SniffFinder
    {
        return new SniffFinder(
            new SniffClassRobotLoaderFactory(),
            new SniffClassFilter()
        );
    }
}
