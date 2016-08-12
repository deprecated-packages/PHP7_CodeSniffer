<?php

namespace Symplify\PHP7_CodeSniffer\Tests\Sniff;

use PHPUnit\Framework\TestCase;
use Symplify\PHP7_CodeSniffer\Sniff\SniffFactory;
use Symplify\PHP7_CodeSniffer\Tests\Instantiator;

final class SniffFactoryTest extends TestCase
{
    /**
     * @var SniffFactory
     */
    private $sniffFactory;

    protected function setUp()
    {
        $this->sniffFactory = Instantiator::createSniffFactory();
    }

    /**
     * @dataProvider provideDataForResolver()
     */
    public function testResolveFromStandardsAndSniffs(
        array $standards,
        array $extraSniffs,
        array $excludedSniffs,
        int $sniffCount
    ) {
        $sniffs = $this->sniffFactory->createFromStandardsAndSniffs(
            $standards,
            $extraSniffs,
            $excludedSniffs
        );

        $this->assertCount($sniffCount, $sniffs);
    }

    public function provideDataForResolver() : array
    {
        return [
            [
                [], [], [], 0
            ], [
                ['PSR2'], [], [], 42
            ], [
                ['PSR2'], ['PEAR.Commenting.ClassComment'], [], 43
            ], [
                ['PSR2'],
                ['PEAR.Commenting.ClassComment'],
                ['PEAR.Commenting.ClassComment', 'PSR2.Namespaces.UseDeclaration'],
                41
            ],
        ];
    }
}
