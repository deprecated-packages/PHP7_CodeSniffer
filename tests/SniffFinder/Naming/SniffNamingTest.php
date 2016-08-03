<?php

namespace Symplify\PHP7_CodeSniffer\SniffFinder\Tests\Naming;

use PHP_CodeSniffer\Standards\PSR2\Sniffs\Classes\ClassDeclarationSniff;
use PHPUnit\Framework\TestCase;
use Symplify\PHP7_CodeSniffer\SniffFinder\Naming\SniffNaming;
use SymplifyCodingStandard\Sniffs\Naming\AbstractClassNameSniff;

class SniffNamingTest extends TestCase
{
    public function testGuessSniffClassBySniffCode()
    {
        $sniffClass = SniffNaming::guessSniffClassBySniffCode('Standard.Category.SniffName');
        $this->assertSame(
            'PHP_CodeSniffer\Standards\Standard\Sniffs\Category\SniffNameSniff',
            $sniffClass
        );
    }

    /**
     * Depends on CodeSniffer 2.5.
     * Temporary down, due to missing Sniff interface in CodeSniffer 3.0.
     */
    public function testCodingStandardAutoload()
    {
        // $sniffClass = SniffNaming::guessSniffClassBySniffCode
        // ('Symplify.Naming.AbstractClassName');
        // $this->assertSame('SymplifyCodingStandard
        // \Sniffs\Naming\AbstractClassNameSniff', $sniffClass);
    }

    public function testGuessSniffCodeByClassName()
    {
        $sniffName = SniffNaming::guessSniffCodeBySniffClass(ClassDeclarationSniff::class);
        $this->assertSame('PSR2.Classes.ClassDeclaration', $sniffName);

        $sniffName = SniffNaming::guessSniffCodeBySniffClass(AbstractClassNameSniff::class);
        $this->assertSame('Symplify.Naming.AbstractClassName', $sniffName);
    }
}
