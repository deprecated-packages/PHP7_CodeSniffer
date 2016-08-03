<?php

namespace Symplify\PHP7_CodeSniffer\Tests\Sniff;

use PHP_CodeSniffer\Standards\PSR2\Sniffs\Classes\ClassDeclarationSniff;
use PHPUnit\Framework\TestCase;
use Symplify\PHP7_CodeSniffer\Sniff\SniffFactory;

final class SniffFactoryTest extends TestCase
{
    public function testCreateFrom()
    {
        $sniffFactory = new SniffFactory();

        $sniffClassNames = [];
        $sniffs = $sniffFactory->createFromSniffClassNames($sniffClassNames);
        $this->assertSame([], $sniffs);

        $sniffClassNames = [
            'Some.Code' => ClassDeclarationSniff::class
        ];
        $sniffs = $sniffFactory->createFromSniffClassNames($sniffClassNames);
        $this->assertSame('Some.Code', key($sniffs));
        $this->assertInstanceOf(ClassDeclarationSniff::class, $sniffs['Some.Code']);
    }
}
