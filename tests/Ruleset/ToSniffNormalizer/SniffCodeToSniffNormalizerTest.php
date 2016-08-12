<?php

namespace Symplify\PHP7_CodeSniffer\Tests\Ruleset\ToSniffNormalizer;

use PHP_CodeSniffer\Standards\PSR2\Sniffs\Classes\ClassDeclarationSniff;
use PHPUnit\Framework\TestCase;
use Symplify\PHP7_CodeSniffer\Ruleset\ToSniffNormalizer\SniffCodeToSniffNormalizer;
use Symplify\PHP7_CodeSniffer\Tests\Instantiator;

final class SniffCodeToSniffNormalizerTest extends TestCase
{
    /**
     * @var SniffCodeToSniffNormalizer
     */
    private $sniffCodeToSniffNormalizer;

    protected function setUp()
    {
        $this->sniffCodeToSniffNormalizer = new SniffCodeToSniffNormalizer(
            Instantiator::createRouter()
        );
    }

    public function testIsMatch()
    {
        $this->assertTrue($this->sniffCodeToSniffNormalizer->isMatch('One.Two.Three'));
        $this->assertFalse($this->sniffCodeToSniffNormalizer->isMatch('fail'));
    }

    public function testNormalizer()
    {
        $sniffCode = 'PSR2.Classes.ClassDeclaration';
        $normalized = $this->sniffCodeToSniffNormalizer->normalize($sniffCode);

        $this->assertSame([
            $sniffCode => ClassDeclarationSniff::class
        ], $normalized);
    }
}
