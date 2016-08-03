<?php

namespace Symplify\PHP7_CodeSniffer\Tests\Composer;

use PHPUnit\Framework\TestCase;
use Symplify\PHP7_CodeSniffer\Composer\VendorDirProvider;

final class VendorDirProviderTest extends TestCase
{
    public function testProvide()
    {
        $this->assertSame(
            realpath(__DIR__.'/../../vendor'),
            VendorDirProvider::provide()
        );
    }
}
