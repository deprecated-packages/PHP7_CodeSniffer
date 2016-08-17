<?php

namespace Symplify\PHP7_CodeSniffer\Tests\Sniff\Factory;

use PHP_CodeSniffer\Standards\Generic\Sniffs\Files\ByteOrderMarkSniff;
use PHP_CodeSniffer\Standards\Generic\Sniffs\Files\LineEndingsSniff;
use PHP_CodeSniffer\Standards\Generic\Sniffs\NamingConventions\UpperCaseConstantNameSniff;
use PHP_CodeSniffer\Standards\Generic\Sniffs\PHP\DisallowShortOpenTagSniff;
use PHP_CodeSniffer\Standards\PSR1\Sniffs\Classes\ClassDeclarationSniff;
use PHPUnit\Framework\TestCase;
use Symplify\PHP7_CodeSniffer\Sniff\Factory\RulesetXmlToSniffsFactory;
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

        $this->assertCount(7, $sniffs);

        $this->assertInstanceOf(ByteOrderMarkSniff::class, $sniffs[0]);
        $this->assertInstanceOf(UpperCaseConstantNameSniff::class, $sniffs[1]);
        $this->assertInstanceOf(DisallowShortOpenTagSniff::class, $sniffs[2]);
        $this->assertInstanceOf(ClassDeclarationSniff::class, $sniffs[3]);
//        $this->assertInstanceOf(ValidClassNameSniff::class, $sniffs[4]);
    }

    public function testCustomRulesetXml()
    {
        $sniffs = $this->rulesetXmlToSniffsFactory->create(
            __DIR__ . '/RulesetXmlSource/ruleset.xml'
        );

        $this->assertCount(1, $sniffs);
        $this->assertInstanceOf(LineEndingsSniff::class, $sniffs[0]);

//        $this->assertEquals([
//            'Generic.Files.ByteOrderMark' => ByteOrderMarkSniff::class,
//            'Generic.Files.LineEndings' => LineEndingsSniff::class,
//            'Generic.NamingConventions.UpperCaseConstantName' => UpperCaseConstantNameSniff::class,
//            'Generic.PHP.DisallowShortOpenTag.EchoFound' => DisallowShortOpenTagSniff::class,
//            'Squiz.Classes.ValidClassName' => ValidClassNameSniff::class,
//        ], $sniffClassNames);
    }
}
