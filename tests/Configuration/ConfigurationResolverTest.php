<?php

namespace Symplify\PHP7_CodeSniffer\Tests\Configuration;

use PHP_CodeSniffer\Standards\PEAR\Sniffs\Commenting\ClassCommentSniff;
use PHPUnit\Framework\TestCase;
use Symplify\PHP7_CodeSniffer\Configuration\ConfigurationResolver;
use Symplify\PHP7_CodeSniffer\Tests\Instantiator;

final class ConfigurationResolverTest extends TestCase
{
    /**
     * @var ConfigurationResolver
     */
    private $configurationResolver;

    protected function setUp()
    {
        $this->configurationResolver = Instantiator::createConfigurationResolver();
    }

    /**
     * @expectedException \Symplify\PHP7_CodeSniffer\Exception\Configuration\OptionResolver\StandardNotFoundException
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
            ['PEAR.Commenting.ClassComment' => ClassCommentSniff::class],
            $this->configurationResolver->resolve('sniffs', ['PEAR.Commenting.ClassComment'])
        );
    }
}
