<?php

namespace Symplify\PHP7_CodeSniffer\Tests\Ruleset\ToSniffNormalizer;

use PHP_CodeSniffer\Standards\Generic\Sniffs\Files\ByteOrderMarkSniff;
use PHP_CodeSniffer\Standards\Generic\Sniffs\NamingConventions\UpperCaseConstantNameSniff;
use PHP_CodeSniffer\Standards\Generic\Sniffs\PHP\DisallowShortOpenTagSniff;
use PHP_CodeSniffer\Standards\PSR1\Sniffs\Files\SideEffectsSniff;
use PHP_CodeSniffer\Standards\PSR1\Sniffs\Methods\CamelCapsMethodNameSniff;
use PHP_CodeSniffer\Standards\PSR1\Sniffs\Classes\ClassDeclarationSniff;
use PHP_CodeSniffer\Standards\Squiz\Sniffs\Classes\ValidClassNameSniff;
use PHPUnit\Framework\TestCase;
use Symplify\PHP7_CodeSniffer\Ruleset\Extractor\CustomPropertyValuesExtractor;
use Symplify\PHP7_CodeSniffer\Ruleset\ToSniffNormalizer\RulesetXmlToSniffNormalizer;
use Symplify\PHP7_CodeSniffer\Ruleset\ToSniffNormalizer\StandardNameToSniffNormalizer;
use Symplify\PHP7_CodeSniffer\Standard\Finder\StandardFinder;
use Symplify\PHP7_CodeSniffer\Tests\Instantiator;

final class StandardNameToSniffNormalizerTest extends TestCase
{
    /**
     * @var StandardNameToSniffNormalizer
     */
    private $standardNameToSniffNormalizer;

    protected function setUp()
    {
        $rulesetXmlToSniffNormalizer = new RulesetXmlToSniffNormalizer(
            Instantiator::createSniffFinder(),
            new CustomPropertyValuesExtractor()
        );

        $referenceNormalizer = Instantiator::createReferenceNormalizer();
        $rulesetXmlToSniffNormalizer->setReferenceNormalizer($referenceNormalizer);

        $this->standardNameToSniffNormalizer = new StandardNameToSniffNormalizer(
            new StandardFinder(),
            $rulesetXmlToSniffNormalizer
        );
    }

    public function testIsMatch()
    {
        $this->assertTrue($this->standardNameToSniffNormalizer->isMatch('PSR1'));
        $this->assertFalse($this->standardNameToSniffNormalizer->isMatch('nonexisting'));
    }

    public function testNormalizer()
    {
        $sniffs = $this->standardNameToSniffNormalizer->normalize('PSR1');

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
}
