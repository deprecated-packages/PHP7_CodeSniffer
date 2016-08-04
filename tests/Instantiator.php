<?php

namespace Symplify\PHP7_CodeSniffer\Tests;

use Symplify\PHP7_CodeSniffer\Configuration\ConfigurationResolver;
use Symplify\PHP7_CodeSniffer\Configuration\OptionResolver\SniffsOptionResolver;
use Symplify\PHP7_CodeSniffer\Configuration\OptionResolver\SourceOptionResolver;
use Symplify\PHP7_CodeSniffer\Configuration\OptionResolver\StandardsOptionResolver;
use Symplify\PHP7_CodeSniffer\Ruleset\Routing\Router;
use Symplify\PHP7_CodeSniffer\Ruleset\Rule\ReferenceNormalizer;
use Symplify\PHP7_CodeSniffer\Ruleset\RulesetBuilder;
use Symplify\PHP7_CodeSniffer\Sniff\Finder\SniffClassFilter;
use Symplify\PHP7_CodeSniffer\Sniff\Finder\SniffClassRobotLoaderFactory;
use Symplify\PHP7_CodeSniffer\Sniff\Finder\SniffFinder;
use Symplify\PHP7_CodeSniffer\Standard\Finder\StandardFinder;

final class Instantiator
{
    public static function createRulesetBuilder() : RulesetBuilder
    {
        return new RulesetBuilder(
            self::createSniffFinder(),
            new StandardFinder(),
            Instantiator::createReferenceNormalizer()
        );
    }

    public static function createReferenceNormalizer() : ReferenceNormalizer
    {
        return new ReferenceNormalizer(
            self::createSniffFinder(),
            new StandardFinder(),
            new Router(self::createSniffFinder())
        );
    }

    public static function createConfigurationResolver() : ConfigurationResolver
    {
        $configurationResolver = new ConfigurationResolver();
        $configurationResolver->addOptionResolver(new StandardsOptionResolver(
            new StandardFinder()
        ));
        $configurationResolver->addOptionResolver(new SniffsOptionResolver());
        $configurationResolver->addOptionResolver(new SourceOptionResolver());

        return $configurationResolver;
    }

    public static function createSniffFinder() : SniffFinder
    {
        return new SniffFinder(
            new SniffClassRobotLoaderFactory(),
            new SniffClassFilter()
        );
    }

    public static function createRuleset() : RulesetBuilder
    {
        return new RulesetBuilder(
            self::createSniffFinder(),
            new StandardFinder(),
            self::createReferenceNormalizer()
        );
    }
}
