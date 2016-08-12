<?php

namespace Symplify\PHP7_CodeSniffer\Tests\Ruleset\ToSniffNormalizer;

use PHP_CodeSniffer\Standards\Generic\Sniffs\Files\ByteOrderMarkSniff;
use PHP_CodeSniffer\Standards\Generic\Sniffs\Files\LineEndingsSniff;
use PHP_CodeSniffer\Standards\Generic\Sniffs\NamingConventions\UpperCaseConstantNameSniff;
use PHP_CodeSniffer\Standards\Generic\Sniffs\PHP\DisallowShortOpenTagSniff;
use PHP_CodeSniffer\Standards\PSR1\Sniffs\Files\SideEffectsSniff;
use PHP_CodeSniffer\Standards\PSR1\Sniffs\Methods\CamelCapsMethodNameSniff;
use PHP_CodeSniffer\Standards\PSR1\Sniffs\Classes\ClassDeclarationSniff;
use PHP_CodeSniffer\Standards\Squiz\Sniffs\Classes\ValidClassNameSniff;
use PHPUnit\Framework\TestCase;
use Symplify\PHP7_CodeSniffer\Ruleset\Extractor\CustomPropertyValuesExtractor;
use Symplify\PHP7_CodeSniffer\Ruleset\ToSniffNormalizer\RulesetXmlToSniffNormalizer;
use Symplify\PHP7_CodeSniffer\Standard\Finder\StandardFinder;
use Symplify\PHP7_CodeSniffer\Tests\Instantiator;

final class RulesetXmlToSniffNormalizerTest extends TestCase
{
    /**
     * @var RulesetXmlToSniffNormalizer
     */
    private $rulesetXmlToSniffNormalizer;

    protected function setUp()
    {
        $this->rulesetXmlToSniffNormalizer = new RulesetXmlToSniffNormalizer(
            Instantiator::createSniffFinder(),
            new CustomPropertyValuesExtractor()
        );

        $referenceNormalizer = Instantiator::createReferenceNormalizer();
        $this->rulesetXmlToSniffNormalizer->setReferenceNormalizer($referenceNormalizer);
    }

    public function testIsMatch()
    {
        $this->assertTrue($this->rulesetXmlToSniffNormalizer->isMatch('ruleset.xml'));
        $this->assertFalse($this->rulesetXmlToSniffNormalizer->isMatch('nonexisting'));
    }

    public function testNormalizer()
    {
        $standardFinder = new StandardFinder();
        $rulesetXmlPath = $standardFinder->getRulesetPathForStandardName('PSR1');

        $sniffs = $this->rulesetXmlToSniffNormalizer->normalize($rulesetXmlPath);

        $this->assertSame([
            'Generic.Files.ByteOrderMark' => ByteOrderMarkSniff::class,
            'Generic.NamingConventions.UpperCaseConstantName' => UpperCaseConstantNameSniff::class,
            'Generic.PHP.DisallowShortOpenTag.EchoFound' => DisallowShortOpenTagSniff::class,
            'PSR1.Classes.ClassDeclaration' => ClassDeclarationSniff::class,
            'PSR1.Files.SideEffects' => SideEffectsSniff::class,
            'PSR1.Methods.CamelCapsMethodName' => CamelCapsMethodNameSniff::class,
            'Squiz.Classes.ValidClassName' => ValidClassNameSniff::class
        ], $sniffs);
    }

    public function testCustomRulesetXml()
    {
        $sniffs = $this->rulesetXmlToSniffNormalizer->normalize(
            __DIR__ . '/RulesetXmlToSniffNormalizerSource/ruleset.xml'
        );

        $this->assertEquals([
            'Generic.Files.ByteOrderMark' => ByteOrderMarkSniff::class,
            'Generic.Files.LineEndings' => LineEndingsSniff::class,
            'Generic.NamingConventions.UpperCaseConstantName' => UpperCaseConstantNameSniff::class,
            'Generic.PHP.DisallowShortOpenTag.EchoFound' => DisallowShortOpenTagSniff::class,
            'PSR1.Classes.ClassDeclaration' => ClassDeclarationSniff::class,
            'PSR1.Files.SideEffects' => SideEffectsSniff::class,
            'PSR1.Methods.CamelCapsMethodName' => CamelCapsMethodNameSniff::class,
            'Squiz.Classes.ValidClassName' => ValidClassNameSniff::class,
        ], $sniffs);
    }
}
