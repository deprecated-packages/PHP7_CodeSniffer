<?php

namespace Symplify\PHP7_CodeSniffer\Tests\Sniff\Factory;

use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Standards\PSR1\Sniffs\Classes\ClassDeclarationSniff;
use PHP_CodeSniffer\Standards\PSR1\Sniffs\Files\SideEffectsSniff;
use PHP_CodeSniffer\Standards\PSR1\Sniffs\Methods\CamelCapsMethodNameSniff;
use PHPUnit\Framework\TestCase;
use Symplify\PHP7_CodeSniffer\Sniff\Factory\RulesetXmlToOwnSniffsFactory;
use Symplify\PHP7_CodeSniffer\Standard\Finder\StandardFinder;
use Symplify\PHP7_CodeSniffer\Tests\Instantiator;

final class RulesetXmlToOwnSniffsFactoryTest extends TestCase
{
    /**
     * @var RulesetXmlToOwnSniffsFactory
     */
    private $rulesetXmlToOwnSniffsFactory;

    protected function setUp()
    {
        $this->rulesetXmlToOwnSniffsFactory = new RulesetXmlToOwnSniffsFactory(
            Instantiator::createSniffFinder(),
            Instantiator::createSingleSniffFactory()
        );
    }

    public function testIsMatch()
    {
        $this->assertTrue($this->rulesetXmlToOwnSniffsFactory->isMatch('ruleset.xml'));
        $this->assertFalse($this->rulesetXmlToOwnSniffsFactory->isMatch('nonexisting'));
    }

    public function testCreate()
    {
        $standardFinder = new StandardFinder();

        $rulesetXmlPath = $standardFinder->getRulesetPathForStandardName('PSR1');
        $sniffs = $this->rulesetXmlToOwnSniffsFactory->create($rulesetXmlPath);

        $this->assertCount(3, $sniffs);
        foreach ($sniffs as $sniff) {
            $this->assertInstanceOf(Sniff::class, $sniff);
        }

        $this->assertInstanceOf(ClassDeclarationSniff::class, $sniffs[0]);
        $this->assertInstanceOf(SideEffectsSniff::class, $sniffs[1]);
        $this->assertInstanceOf(CamelCapsMethodNameSniff::class, $sniffs[2]);
    }

    public function testCustomRulesetXml()
    {
        $sniffs = $this->rulesetXmlToOwnSniffsFactory->create(
            __DIR__ . '/RulesetXmlSource/ruleset.xml'
        );

        $this->assertEmpty($sniffs);
    }
}
