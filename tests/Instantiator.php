<?php

namespace Symplify\PHP7_CodeSniffer\Tests;

use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Tests\Output\TestOutput;
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
use Symplify\PHP7_CodeSniffer\Fixer;
use Symplify\PHP7_CodeSniffer\Parser\EolCharDetector;
use Symplify\PHP7_CodeSniffer\Parser\FileToTokensParser;
use Symplify\PHP7_CodeSniffer\Php7CodeSniffer;
use Symplify\PHP7_CodeSniffer\Report\ErrorDataCollector;
use Symplify\PHP7_CodeSniffer\Report\ErrorMessageSorter;
use Symplify\PHP7_CodeSniffer\Ruleset\Extractor\CustomPropertyValuesExtractor;
use Symplify\PHP7_CodeSniffer\Ruleset\Routing\Router;
use Symplify\PHP7_CodeSniffer\Ruleset\Rule\ReferenceNormalizer;
use Symplify\PHP7_CodeSniffer\Ruleset\ToSniffNormalizer\RulesetXmlToSniffNormalizer;
use Symplify\PHP7_CodeSniffer\Ruleset\ToSniffNormalizer\SniffCodeToSniffNormalizer;
use Symplify\PHP7_CodeSniffer\Ruleset\ToSniffNormalizer\StandardNameToSniffNormalizer;
use Symplify\PHP7_CodeSniffer\Sniff\Finder\SniffClassFilter;
use Symplify\PHP7_CodeSniffer\Sniff\Finder\SniffClassRobotLoaderFactory;
use Symplify\PHP7_CodeSniffer\Sniff\Finder\SniffFinder;
use Symplify\PHP7_CodeSniffer\Sniff\SniffClassesResolver;
use Symplify\PHP7_CodeSniffer\Sniff\SniffFactory;
use Symplify\PHP7_CodeSniffer\Standard\Finder\StandardFinder;

final class Instantiator
{
    public static function createReferenceNormalizer() : ReferenceNormalizer
    {
        $referenceNormalizer = new ReferenceNormalizer(
            self::createSniffFinder(),
            new StandardFinder(),
            new Router(self::createSniffFinder())
        );

        $rulesetXmlToSniffNormalizer = new RulesetXmlToSniffNormalizer(
            self::createSniffFinder(),
            new CustomPropertyValuesExtractor()
        );
        $rulesetXmlToSniffNormalizer->setReferenceNormalizer($referenceNormalizer);

        $referenceNormalizer->addNormalizer(new SniffCodeToSniffNormalizer(self::createRouter()));
        $referenceNormalizer->addNormalizer($rulesetXmlToSniffNormalizer);
        $referenceNormalizer->addNormalizer(
            new StandardNameToSniffNormalizer(
                new StandardFinder(),
                $rulesetXmlToSniffNormalizer
            )
        );

        return $referenceNormalizer;
    }

    public static function createRulesetXmlToSniffNormalizer() : RulesetXmlToSniffNormalizer
    {
        $rulesetXmlToSniffNormalizer = new RulesetXmlToSniffNormalizer(
            self::createSniffFinder(),
            new CustomPropertyValuesExtractor()
        );
        $rulesetXmlToSniffNormalizer->setReferenceNormalizer(self::createReferenceNormalizer());

        return $rulesetXmlToSniffNormalizer;
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
            self::createRulesetXmlToSniffNormalizer()
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
}
