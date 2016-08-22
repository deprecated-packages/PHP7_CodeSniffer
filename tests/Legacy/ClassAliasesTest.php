<?php

namespace Symplify\PHP7_CodeSniffer\Tests\Legacy;

use PHPUnit\Framework\TestCase;
use Symplify\PHP7_CodeSniffer\Legacy\ClassAliases;

final class ClassAliasesTest extends TestCase
{
    public function testRegisterAliases()
    {
        ClassAliases::registerAliases();

        $this->assertTrue(class_exists('PHP_CodeSniffer_File'));
        $this->assertTrue(interface_exists('PHP_CodeSniffer_Sniff'));

        $this->assertTrue(class_exists('PHP_CodeSniffer_Standards_AbstractVariableSniff'));
        $this->assertTrue(class_exists('PEAR_Sniffs_Commenting_ClassCommentSniff'));
    }
}
