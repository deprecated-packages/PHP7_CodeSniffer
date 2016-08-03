<?php

namespace Symplify\PHP7_CodeSniffer\Ruleset\Tests;

use PHP_CodeSniffer\Util\Tokens;
use PHPUnit\Framework\TestCase;
use Symplify\PHP7_CodeSniffer\Ruleset\Routing\Router;
use Symplify\PHP7_CodeSniffer\Ruleset\Rule\ReferenceNormalizer;
use Symplify\PHP7_CodeSniffer\Ruleset\RulesetBuilder;
use Symplify\PHP7_CodeSniffer\SniffFinder\SniffClassFilter;
use Symplify\PHP7_CodeSniffer\SniffFinder\SniffClassRobotLoaderFactory;
use Symplify\PHP7_CodeSniffer\SniffFinder\SniffFinder;
use Symplify\PHP7_CodeSniffer\Standard\StandardFinder;

final class RulesetBuilderTest extends TestCase
{
    /**
     * @var RulesetBuilder
     */
    private $rulesetBuilder;

    protected function setUp()
    {
        new Tokens();
        $sniffFinder = $this->createSniffFinder();

        $this->rulesetBuilder = new RulesetBuilder(
            $sniffFinder,
            new StandardFinder(),
            new ReferenceNormalizer($sniffFinder, new StandardFinder(), new Router($sniffFinder))
        );
    }

    public function testBuildFromRulesetXml()
    {
        $ruleset = $this->rulesetBuilder->buildFromRulesetXml(__DIR__ . '/RulesetBuilderSource/ruleset.xml');
        $this->assertInternalType('array', $ruleset);

        $this->assertSame([
           'PSR1.Classes.ClassDeclaration' => 'PHP_CodeSniffer\Standards\PSR1\Sniffs\Classes\ClassDeclarationSniff',
           'PSR1.Files.SideEffects' => 'PHP_CodeSniffer\Standards\PSR1\Sniffs\Files\SideEffectsSniff',
           'PSR1.Methods.CamelCapsMethodName' => 'PHP_CodeSniffer\Standards\PSR1\Sniffs\Methods\CamelCapsMethodNameSniff',
           'Generic.PHP.DisallowShortOpenTag.EchoFound' => 'PHP_CodeSniffer\Standards\Generic\Sniffs\PHP\DisallowShortOpenTagSniff',
           'Generic.Files.ByteOrderMark' => 'PHP_CodeSniffer\Standards\Generic\Sniffs\Files\ByteOrderMarkSniff',
           'Squiz.Classes.ValidClassName' => 'PHP_CodeSniffer\Standards\Squiz\Sniffs\Classes\ValidClassNameSniff',
           'Generic.NamingConventions.UpperCaseConstantName' => 'PHP_CodeSniffer\Standards\Generic\Sniffs\NamingConventions\UpperCaseConstantNameSniff',
           'Generic.Files.LineEndings' => 'PHP_CodeSniffer\Standards\Generic\Sniffs\Files\LineEndingsSniff',
        ], $ruleset);
    }

    private function createSniffFinder() : SniffFinder
    {
        return new SniffFinder(
            new SniffClassRobotLoaderFactory(),
            new SniffClassFilter()
        );
    }
}
