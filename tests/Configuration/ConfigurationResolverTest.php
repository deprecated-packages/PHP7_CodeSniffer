<?php

namespace Symplify\PHP7_CodeSniffer\Configuration\Tests;

use Exception;
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
     * @expectedException Exception
     */
    public function testNonExistingStandard()
    {
        $this->configurationResolver->resolveStandards(['fake']);
    }
}
