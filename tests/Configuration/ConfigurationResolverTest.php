<?php

namespace Symplify\PHP7_CodeSniffer\Configuration\Tests;

use PHPUnit\Framework\TestCase;
use Symplify\PHP7_CodeSniffer\Configuration\ConfigurationResolver;
use Symplify\PHP7_CodeSniffer\Configuration\OptionResolver\SniffsOptionResolver;
use Symplify\PHP7_CodeSniffer\Configuration\OptionResolver\SourceOptionResolver;
use Symplify\PHP7_CodeSniffer\Configuration\OptionResolver\StandardsOptionResolver;
use Symplify\PHP7_CodeSniffer\Configuration\OptionResolverFactory;
use Symplify\PHP7_CodeSniffer\Standard\StandardFinder;

final class ConfigurationResolverTest extends TestCase
{
    /**
     * @var ConfigurationResolver
     */
    private $configurationResolver;

    protected function setUp()
    {
        $this->configurationResolver = new ConfigurationResolver();
        $this->configurationResolver->addOptionResolver(new SniffsOptionResolver());
        $this->configurationResolver->addOptionResolver(new SourceOptionResolver());
        $this->configurationResolver->addOptionResolver(new StandardsOptionResolver(
            new StandardFinder()
        ));
    }

    /**
     * @expectedException \Symplify\PHP7_CodeSniffer\Exception\Configuration\StandardNotFoundException
     */
    public function testNonExistingStandard()
    {
        $this->configurationResolver->resolve('standards', ['fake']);
    }

    public function testResolve()
    {
        $this->assertSame(
            [__DIR__],
            $this->configurationResolver->resolve('source', [__DIR__])
        );

        $this->assertArrayHasKey(
            'PSR2',
            $this->configurationResolver->resolve('standards', ['PSR2'])
        );

        $this->assertSame(
            ['One.Two.Three'],
            $this->configurationResolver->resolve('sniffs', ['One.Two.Three'])
        );
    }
}
