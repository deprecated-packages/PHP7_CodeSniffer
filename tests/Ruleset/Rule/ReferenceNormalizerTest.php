<?php

namespace Symplify\PHP7_CodeSniffer\Ruleset\Tests\Rule;

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
        $this->referenceNormalizer = Instantiator::createReferenceNormalizer();
    }

    public function testIsStandardReference()
    {
        $this->referenceNormalizer->normalize('PSR2');
        $this->referenceNormalizer->normalize('ruleset.xml');
    }
}
