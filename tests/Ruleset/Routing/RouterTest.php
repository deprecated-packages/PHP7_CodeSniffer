<?php

namespace Symplify\PHP7_CodeSniffer\Tests\Ruleset\Routing;

use PHP_CodeSniffer\Standards\PSR2\Sniffs\Classes\ClassDeclarationSniff;
use PHPUnit\Framework\TestCase;
use Symplify\PHP7_CodeSniffer\Ruleset\Routing\Router;
use Symplify\PHP7_CodeSniffer\Tests\Instantiator;

final class RouterTest extends TestCase
{
    /**
     * @var Router
     */
    private $router;

    protected function setUp()
    {
        $this->router = new Router(Instantiator::createSniffFinder());
    }

    public function testGetClassFromSniffName()
    {
        $this->assertSame(
            ClassDeclarationSniff::class,
            $this->router->getClassFromSniffName('PSR2.Classes.ClassDeclaration')
        );
    }

    public function testGetClassFromSniffNameRandom()
    {
        $this->assertSame('', $this->router->getClassFromSniffName(random_bytes(65)));
    }
}