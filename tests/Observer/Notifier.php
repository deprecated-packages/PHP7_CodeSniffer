<?php

namespace Symplify\PHP7_CodeSniffer\Tests\Observer;

class Notifier
{
    private $observers = array();

    public function register(ObserverInterface $observer) {
        $this->observers[] = $observer;
    }

    public function notify()
    {
        foreach ($this->observers AS $observer) {
            $observer->doSmt();
        }
    }
}
