<?php

namespace Symplify\PHP7_CodeSniffer\Tests\Sniff;

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
    }
}
