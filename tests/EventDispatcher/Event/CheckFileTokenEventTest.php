<?php

namespace Symplify\PHP7_CodeSniffer\Tests\EventDispatcher\Event;

use PHPUnit\Framework\TestCase;
use Symplify\PHP7_CodeSniffer\Contract\File\FileInterface;
use Symplify\PHP7_CodeSniffer\EventDispatcher\Event\CheckFileTokenEvent;

final class CheckFileTokenEventTest extends TestCase
{
    public function test()
    {
        $fileMock = $this->prophesize(FileInterface::class)->reveal();
        $event = new CheckFileTokenEvent($fileMock, 5);

        $this->assertSame($fileMock, $event->getFile());
        $this->assertSame(5, $event->getStackPointer());
    }
}
