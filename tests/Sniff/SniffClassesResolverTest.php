<?php

namespace Symplify\PHP7_CodeSniffer\Tests\Sniff;

use PHP_CodeSniffer\Util\Tokens;
use PHPUnit\Framework\TestCase;
use Symplify\PHP7_CodeSniffer\Sniff\SniffClassesResolver;
use Symplify\PHP7_CodeSniffer\Tests\Instantiator;

final class SniffClassesResolverTest extends TestCase
{
    /**
     * @var SniffClassesResolver
     */
    private $sniffClassesResolver;

    protected function setUp()
    {
        new Tokens();

        $this->sniffClassesResolver = new SniffClassesResolver(
            Instantiator::createConfigurationResolver(),
            Instantiator::createRulesetBuilder()
        );
    }

    public function testResolveFromStandardsAndSniffs()
    {
        $this->assertSame(
            [],
            $this->sniffClassesResolver->resolveFromStandardsAndSniffs([], [])
        );

        $sniffList = $this->sniffClassesResolver->resolveFromStandardsAndSniffs(['PSR2'], []);
        $this->assertCount(43, $sniffList);

        $sniffList = $this->sniffClassesResolver->resolveFromStandardsAndSniffs(
            ['PSR2'],
            ['PEAR.Commenting.ClassComment']
        );
        $this->assertCount(44, $sniffList);
    }
}
