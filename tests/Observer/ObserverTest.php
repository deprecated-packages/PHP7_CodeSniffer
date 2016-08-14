<?php

namespace Symplify\PHP7_CodeSniffer\Tests\Observer;

use PHPUnit\Framework\TestCase;

final class ObserverTest extends TestCase
{
    public function test()
    {

        $a = new A();
        $b = new B();

        $notifier = new Notifier();
        $notifier->register($a);
        $notifier->register($b);

        $notifier->notify();
    }
}
