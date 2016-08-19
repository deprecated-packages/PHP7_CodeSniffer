<?php

namespace Symplify\PHP7_CodeSniffer\Tests\Sniff\Factory;

use PHP_CodeSniffer\Standards\PSR2\Sniffs\Classes\ClassDeclarationSniff;
use PHPUnit\Framework\TestCase;
use Symplify\PHP7_CodeSniffer\Sniff\Factory\SingleSniffFactory;
use Symplify\PHP7_CodeSniffer\Tests\Instantiator;

final class SingleSniffFactoryTest extends TestCase
{
    /**
     * @var SingleSniffFactory
     */
    private $singleSniffFactory;

    protected function setUp()
    {
        $this->singleSniffFactory = Instantiator::createSingleSniffFactory();
    }

    /**
     * @expectedException \Symplify\PHP7_CodeSniffer\Exception\Sniff\Naming\InvalidSniffClassException
     */
    public function testCreateInvalidClassName()
    {
        $this->singleSniffFactory->create('mmissing');
    }

    public function testCreate()
    {
        $sniff = $this->singleSniffFactory->create(ClassDeclarationSniff::class);
        $this->assertInstanceOf(ClassDeclarationSniff::class, $sniff);
    }
}
