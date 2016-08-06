<?php

namespace Symplify\PHP7_CodeSniffer\Tests;

use Symplify\PHP7_CodeSniffer\Configuration\ConfigurationResolver;
use Symplify\PHP7_CodeSniffer\Configuration\OptionResolver\SniffsOptionResolver;
use Symplify\PHP7_CodeSniffer\Configuration\OptionResolver\SourceOptionResolver;
use Symplify\PHP7_CodeSniffer\Configuration\OptionResolver\StandardsOptionResolver;
use Symplify\PHP7_CodeSniffer\EventDispatcher\CurrentListenerSniffCodeProvider;
use Symplify\PHP7_CodeSniffer\EventDispatcher\SniffDispatcher;
use Symplify\PHP7_CodeSniffer\File\FileFactory;
use Symplify\PHP7_CodeSniffer\File\Finder\SourceFinder;
use Symplify\PHP7_CodeSniffer\File\Provider\FilesProvider;
use Symplify\PHP7_CodeSniffer\Fixer;
use Symplify\PHP7_CodeSniffer\Parser\EolCharDetector;
use Symplify\PHP7_CodeSniffer\Parser\FileToTokensParser;
use Symplify\PHP7_CodeSniffer\Php7CodeSniffer;
use Symplify\PHP7_CodeSniffer\Report\ErrorDataCollector;
use Symplify\PHP7_CodeSniffer\Report\ErrorMessageSorter;
use Symplify\PHP7_CodeSniffer\Ruleset\Extractor\CustomPropertyValuesExtractor;
use Symplify\PHP7_CodeSniffer\Ruleset\Routing\Router;
use Symplify\PHP7_CodeSniffer\Ruleset\Rule\ReferenceNormalizer;
use Symplify\PHP7_CodeSniffer\Ruleset\RulesetBuilder;
use Symplify\PHP7_CodeSniffer\Sniff\Finder\SniffClassFilter;
use Symplify\PHP7_CodeSniffer\Sniff\Finder\SniffClassRobotLoaderFactory;
use Symplify\PHP7_CodeSniffer\Sniff\Finder\SniffFinder;
use Symplify\PHP7_CodeSniffer\Sniff\SniffClassesResolver;
use Symplify\PHP7_CodeSniffer\Sniff\SniffFactory;
use Symplify\PHP7_CodeSniffer\Standard\Finder\StandardFinder;

final class Instantiator
{
    public static function createRulesetBuilder() : RulesetBuilder
    {
        return new RulesetBuilder(
            self::createSniffFinder(),
            new StandardFinder(),
            self::createReferenceNormalizer(),
            new CustomPropertyValuesExtractor()
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

    public static function createFileFactory() : FileFactory
    {
        return new FileFactory(
            new Fixer(),
            self::createErrorDataCollector(),
            new FileToTokensParser(new EolCharDetector()),
            new EolCharDetector()
        );
    }

    private static function createErrorDataCollector() : ErrorDataCollector
    {
        return new ErrorDataCollector(
            new CurrentListenerSniffCodeProvider(),
            new ErrorMessageSorter()
        );
    }

    public static function createPhp7CodeSniffer() : Php7CodeSniffer
    {
        return new Php7CodeSniffer(
            new SniffDispatcher(new CurrentListenerSniffCodeProvider()),
            new FilesProvider(new SourceFinder(), self::createFileFactory()),
            self::createSniffCassesResolver(),
            new SniffFactory()
        );
    }

    public static function createSniffCassesResolver() : SniffClassesResolver
    {
        return new SniffClassesResolver(
            self::createConfigurationResolver(),
            self::createRulesetBuilder()
        );
    }
}
