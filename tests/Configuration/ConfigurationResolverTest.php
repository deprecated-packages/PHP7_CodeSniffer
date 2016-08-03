<?php

namespace Symplify\PHP7_CodeSniffer\Configuration\Tests;

use PHPUnit\Framework\TestCase;
use Symplify\PHP7_CodeSniffer\Configuration\ConfigurationResolver;
use Symplify\PHP7_CodeSniffer\Configuration\OptionsResolverFactory;
use Symplify\PHP7_CodeSniffer\Standard\StandardFinder;

final class ConfigurationResolverTest extends TestCase
{
    /**
     * @var ConfigurationResolver
     */
    private $configurationResolver;

    protected function setUp()
    {
        $optionsResolver = (new OptionsResolverFactory(new StandardFinder()))->create();
        $this->configurationResolver = new ConfigurationResolver($optionsResolver);
    }

    /**
     * @expectedException \Symplify\PHP7_CodeSniffer\Exception\Configuration\StandardNotFoundException
     * @expectedExceptionMessage Standard "fake" is not supported. Pick one of: PSR1, MySource, PSR2, Zend, PEAR, Squiz, Generic.
     */
    public function testNonExistingStandard()
    {
        $this->configurationResolver->resolveStandards(['fake']);
    }

    public function test()
    {
        $this->assertSame(
            [__DIR__],
            $this->configurationResolver->resolveSource([__DIR__])
        );

        $this->assertArrayHasKey(
            'PSR2',
            $this->configurationResolver->resolveStandards(['PSR2'])
        );

        $this->assertSame(
            ['One.Two.Three'],
            $this->configurationResolver->resolveSniffs(['One.Two.Three'])
        );
    }
}
