<?php

namespace Symplify\PHP7_CodeSniffer\Tests;

use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Tests\Output\TestOutput;
use Symplify\PHP7_CodeSniffer\Application\Application;
use Symplify\PHP7_CodeSniffer\Application\FileProcessor;
use Symplify\PHP7_CodeSniffer\Application\Fixer;
use Symplify\PHP7_CodeSniffer\Configuration\ConfigurationResolver;
use Symplify\PHP7_CodeSniffer\Configuration\OptionResolver\SniffsOptionResolver;
use Symplify\PHP7_CodeSniffer\Configuration\OptionResolver\SourceOptionResolver;
use Symplify\PHP7_CodeSniffer\Configuration\OptionResolver\StandardsOptionResolver;
use Symplify\PHP7_CodeSniffer\Console\Style\CodeSnifferStyle;
use Symplify\PHP7_CodeSniffer\EventDispatcher\CurrentListenerSniffCodeProvider;
use Symplify\PHP7_CodeSniffer\EventDispatcher\SniffDispatcher;
use Symplify\PHP7_CodeSniffer\File\FileFactory;
use Symplify\PHP7_CodeSniffer\File\Finder\SourceFinder;
use Symplify\PHP7_CodeSniffer\File\Provider\FilesProvider;
use Symplify\PHP7_CodeSniffer\Parser\EolCharDetector;
use Symplify\PHP7_CodeSniffer\Parser\FileToTokensParser;
use Symplify\PHP7_CodeSniffer\Report\ErrorDataCollector;
use Symplify\PHP7_CodeSniffer\Report\ErrorMessageSorter;
use Symplify\PHP7_CodeSniffer\Sniff\Factory\RulesetXmlToOwnSniffsFactory;
use Symplify\PHP7_CodeSniffer\Sniff\Factory\RulesetXmlToSniffsFactory;
use Symplify\PHP7_CodeSniffer\Sniff\Factory\SingleSniffFactory;
use Symplify\PHP7_CodeSniffer\Sniff\Factory\SniffCodeToSniffsFactory;
use Symplify\PHP7_CodeSniffer\Sniff\Routing\Router;
use Symplify\PHP7_CodeSniffer\Sniff\Finder\SniffClassFilter;
use Symplify\PHP7_CodeSniffer\Sniff\Finder\SniffClassRobotLoaderFactory;
use Symplify\PHP7_CodeSniffer\Sniff\Finder\SniffFinder;
use Symplify\PHP7_CodeSniffer\Sniff\SniffSetFactory;
use Symplify\PHP7_CodeSniffer\Sniff\Xml\DataCollector\CustomSniffPropertyValueDataCollector;
use Symplify\PHP7_CodeSniffer\Sniff\Xml\DataCollector\ExcludedSniffDataCollector;
use Symplify\PHP7_CodeSniffer\Standard\Finder\StandardFinder;

final class Instantiator
{
    public static function createRulesetXmlToSniffsFactory() : RulesetXmlToSniffsFactory
    {
        $rulesetXmlToSniffsFactory = new RulesetXmlToSniffsFactory(
            self::createSniffFinder(),
            new ExcludedSniffDataCollector(),
            new CustomSniffPropertyValueDataCollector(),
            self::createSingleSniffFactory()
        );

        $rulesetXmlToSniffsFactory->setSniffSetFactory(self::createSniffSetFactory());

        return $rulesetXmlToSniffsFactory;
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

    public static function createFileFactory() : FileFactory
    {
        return new FileFactory(
            new Fixer(),
            self::createErrorDataCollector(),
            new FileToTokensParser(new EolCharDetector()),
            new EolCharDetector()
        );
    }

    public static function createErrorDataCollector() : ErrorDataCollector
    {
        return new ErrorDataCollector(
            new CurrentListenerSniffCodeProvider(),
            new ErrorMessageSorter()
        );
    }

    public static function createPhp7CodeSniffer() : Application
    {
        return new Application(
            self::createSniffDispatcher(),
            new FilesProvider(new SourceFinder(), self::createFileFactory()),
            self::createSniffSetFactory(),
            new ExcludedSniffDataCollector(),
            self::createConfigurationResolver(),
            new FileProcessor(self::createSniffDispatcher(), new Fixer())
        );
    }

    public static function createCodeSnifferStyle() : CodeSnifferStyle
    {
        return new CodeSnifferStyle(
            new ArgvInput(),
            new TestOutput()
        );
    }

    public static function createRouter() : Router
    {
        return new Router(self::createSniffFinder());
    }

    public static function createSniffSetFactory() : SniffSetFactory
    {
        $sniffSetFactory = new SniffSetFactory(
            self::createConfigurationResolver()
        );
        $sniffSetFactory->addSniffFactory(new SniffCodeToSniffsFactory(
            self::createRouter(),
            self::createSingleSniffFactory()
        ));

        return $sniffSetFactory;
    }

    public static function createSingleSniffFactory() : SingleSniffFactory
    {
        return new SingleSniffFactory(
            new ExcludedSniffDataCollector(),
            new CustomSniffPropertyValueDataCollector()
        );
    }

    private static function createSniffDispatcher() : SniffDispatcher
    {
        return new SniffDispatcher(new CurrentListenerSniffCodeProvider());
    }
}
