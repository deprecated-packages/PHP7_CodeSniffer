<?php

namespace Symplify\PHP7_CodeSniffer\Configuration\Tests;

use PHPUnit\Framework\TestCase;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symplify\PHP7_CodeSniffer\Configuration\ConfigurationResolver;
use Symplify\PHP7_CodeSniffer\Configuration\OptionsResolverFactory;
use Symplify\PHP7_CodeSniffer\Standard\StandardFinder;

final class OptionsResolverFactoryTest extends TestCase
{
    /**
     * @var OptionsResolver
     */
    private $optionsResolver;

    protected function setUp()
    {
        $this->optionsResolver = (new OptionsResolverFactory(new StandardFinder()))->create();
    }

    public function testResolve()
    {
        $options = $this->optionsResolver->resolve([
            'source' => [__DIR__],
            'standards' => ['PSR2'],
            'sniffs' => ['One.Two.Three']
        ]);

        $this->assertSame([__DIR__], $options['source']);
        $this->assertArrayHasKey('PSR2', $options['standards']);
        $this->assertSame(['One.Two.Three'], $options['sniffs']);
    }
}
