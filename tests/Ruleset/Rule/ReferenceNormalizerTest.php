<?php

namespace Symplify\PHP7_CodeSniffer\Ruleset\Tests\Rule;

use PHP_CodeSniffer\Util\Tokens;
use PHPUnit\Framework\TestCase;
use Symplify\PHP7_CodeSniffer\Ruleset\Rule\ReferenceNormalizer;
use Symplify\PHP7_CodeSniffer\Tests\Instantiator;

final class ReferenceNormalizerTest extends TestCase
{
    /**
     * @var ReferenceNormalizer
     */
    private $referenceNormalizer;

    protected function setUp()
    {
        new Tokens();

        $this->referenceNormalizer = Instantiator::createReferenceNormalizer();
    }

    public function testIsStandardReference()
    {
        $this->assertTrue($this->referenceNormalizer->isStandardReference('PSR1'));
        $this->assertFalse($this->referenceNormalizer->isStandardReference('non-existing'));
    }
}
