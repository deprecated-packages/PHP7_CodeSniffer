<?php

namespace Symplify\PHP7_CodeSniffer\Tests\Sniff\Factory;

use PHP_CodeSniffer\Standards\Generic\Sniffs\Files\ByteOrderMarkSniff;
use PHP_CodeSniffer\Standards\Generic\Sniffs\Files\LineEndingsSniff;
use PHP_CodeSniffer\Standards\Generic\Sniffs\NamingConventions\UpperCaseConstantNameSniff;
use PHP_CodeSniffer\Standards\Generic\Sniffs\PHP\DisallowShortOpenTagSniff;
use PHP_CodeSniffer\Standards\PSR1\Sniffs\Files\SideEffectsSniff;
use PHP_CodeSniffer\Standards\PSR1\Sniffs\Methods\CamelCapsMethodNameSniff;
use PHP_CodeSniffer\Standards\PSR1\Sniffs\Classes\ClassDeclarationSniff;
use PHP_CodeSniffer\Standards\Squiz\Sniffs\Classes\ValidClassNameSniff;
use PHPUnit\Framework\TestCase;
use Symplify\PHP7_CodeSniffer\Sniff\Xml\DataCollector\CustomSniffPropertyValueDataCollector;
use Symplify\PHP7_CodeSniffer\Sniff\Xml\DataCollector\ExcludedSniffDataCollector;
use Symplify\PHP7_CodeSniffer\Sniff\Factory\RulesetXmlToSniffsFactory;
use Symplify\PHP7_CodeSniffer\Sniff\Xml\Extractor\CustomSniffPropertyValuesExtractor;
use Symplify\PHP7_CodeSniffer\Standard\Finder\StandardFinder;
use Symplify\PHP7_CodeSniffer\Tests\Instantiator;

final class RulesetXmlToSniffsFactoryTest extends TestCase
{
    /**
     * @var RulesetXmlToSniffsFactory
     */
    private $rulesetXmlToSniffsFactory;

    protected function setUp()
    {
        $this->rulesetXmlToSniffsFactory = Instantiator::createRulesetXmlToSniffsFactory();
    }

    public function testIsMatch()
    {
        $this->assertTrue($this->rulesetXmlToSniffsFactory->isMatch('ruleset.xml'));
        $this->assertFalse($this->rulesetXmlToSniffsFactory->isMatch('nonexisting'));
    }

    public function testCreate()
    {
        $standardFinder = new StandardFinder();

        $rulesetXmlPath = $standardFinder->getRulesetPathForStandardName('PSR1');
        $sniffs = $this->rulesetXmlToSniffsFactory->create($rulesetXmlPath);

        $this->assertCount(4, $sniffs);

//        $this->assertSame([
//            'Generic.Files.ByteOrderMark' => ByteOrderMarkSniff::class,
//            'Generic.NamingConventions.UpperCaseConstantName' => UpperCaseConstantNameSniff::class,
//            'Squiz.Classes.ValidClassName' => ValidClassNameSniff::class
//        ], $sniffClassNames);
    }

//    public function testCustomRulesetXml()
//    {
//        $sniffClassNames = $this->rulesetXmlToSniffsFactory->create(
//            __DIR__ . '/RulesetXmlToSniffNormalizerSource/ruleset.xml'
//        );
//
//        $this->assertEquals([
//            'Generic.Files.ByteOrderMark' => ByteOrderMarkSniff::class,
//            'Generic.Files.LineEndings' => LineEndingsSniff::class,
//            'Generic.NamingConventions.UpperCaseConstantName' => UpperCaseConstantNameSniff::class,
//            'Generic.PHP.DisallowShortOpenTag.EchoFound' => DisallowShortOpenTagSniff::class,
//            'PSR1.Classes.ClassDeclaration' => ClassDeclarationSniff::class,
//            'PSR1.Files.SideEffects' => SideEffectsSniff::class,
//            'PSR1.Methods.CamelCapsMethodName' => CamelCapsMethodNameSniff::class,
//            'Squiz.Classes.ValidClassName' => ValidClassNameSniff::class,
//        ], $sniffClassNames);
//    }
}
