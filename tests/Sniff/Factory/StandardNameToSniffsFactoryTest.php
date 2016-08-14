<?php

namespace Symplify\PHP7_CodeSniffer\Tests\Sniff\Factory;

use PHP_CodeSniffer\Standards\Generic\Sniffs\Files\ByteOrderMarkSniff;
use PHP_CodeSniffer\Standards\Generic\Sniffs\NamingConventions\UpperCaseConstantNameSniff;
use PHP_CodeSniffer\Standards\Generic\Sniffs\PHP\DisallowShortOpenTagSniff;
use PHP_CodeSniffer\Standards\PSR1\Sniffs\Files\SideEffectsSniff;
use PHP_CodeSniffer\Standards\PSR1\Sniffs\Methods\CamelCapsMethodNameSniff;
use PHP_CodeSniffer\Standards\PSR1\Sniffs\Classes\ClassDeclarationSniff;
use PHP_CodeSniffer\Standards\Squiz\Sniffs\Classes\ValidClassNameSniff;
use PHPUnit\Framework\TestCase;
use Symplify\PHP7_CodeSniffer\Sniff\Factory\RulesetXmlToSniffsFactory;
use Symplify\PHP7_CodeSniffer\Sniff\Factory\StandardNameToSniffsFactory;
use Symplify\PHP7_CodeSniffer\Sniff\Xml\DataCollector\CustomSniffPropertyValueDataCollector;
use Symplify\PHP7_CodeSniffer\Sniff\Xml\DataCollector\ExcludedSniffDataCollector;
use Symplify\PHP7_CodeSniffer\Standard\Finder\StandardFinder;
use Symplify\PHP7_CodeSniffer\Tests\Instantiator;

final class StandardNameToSniffsFactoryTest extends TestCase
{
    /**
     * @var StandardNameToSniffsFactory
     */
    private $standardNameToSniffsFactory;

    protected function setUp()
    {
        $rulesetXmlToSniffsFactory = new RulesetXmlToSniffsFactory(
            new ExcludedSniffDataCollector(),
            new CustomSniffPropertyValueDataCollector()
        );

        $sniffSetFactory = Instantiator::createSniffSetFactory();
        $rulesetXmlToSniffsFactory->sniffFactoryCollector($sniffSetFactory);

        $this->standardNameToSniffsFactory = new StandardNameToSniffsFactory(
            new StandardFinder(),
            Instantiator::createRulesetXmlToSniffsFactory(),
            Instantiator::createRulesetXmlToOwnSniffsFactory()
        );
    }

    public function testIsMatch()
    {
        $this->assertTrue($this->standardNameToSniffsFactory->isMatch('PSR1'));
        $this->assertFalse($this->standardNameToSniffsFactory->isMatch('nonexisting'));
    }

    public function testCreate()
    {
        $sniffs = $this->standardNameToSniffsFactory->create('PSR1');
        $this->assertCount(7, $sniffs);

        $this->assertInstanceOf(ByteOrderMarkSniff::class, $sniffs[0]);
        $this->assertInstanceOf(UpperCaseConstantNameSniff::class, $sniffs[1]);
        $this->assertInstanceOf(DisallowShortOpenTagSniff::class, $sniffs[2]);
        $this->assertInstanceOf(ValidClassNameSniff::class, $sniffs[3]);
        $this->assertInstanceOf(ClassDeclarationSniff::class, $sniffs[4]);
        $this->assertInstanceOf(SideEffectsSniff::class, $sniffs[5]);
        $this->assertInstanceOf(CamelCapsMethodNameSniff::class, $sniffs[6]);
    }
}
