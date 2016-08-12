<?php

namespace Symplify\PHP7_CodeSniffer\Ruleset\Tests\Rule;

use PHPUnit\Framework\TestCase;
use stdClass;
use Symplify\PHP7_CodeSniffer\Ruleset\Rule\ReferenceNormalizer;
use Symplify\PHP7_CodeSniffer\Tests\Instantiator;
use TypeError;

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

    /**
     * @expectedException TypeError
     */
    public function testAddNormalizer()
    {
        $this->referenceNormalizer->addNormalizer(new stdClass());
    }

    public function testNoramlize()
    {
        $this->referenceNormalizer->normalize('PSR2');
    }
}
