<?php

/*
 * This file is part of Symplify
 * Copyright (c) 2016 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\PHP7_CodeSniffer\EventDispatcher;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventDispatcher as BaseEventDispatcher;

final class CurrentListenerAwareEventDispatcher extends BaseEventDispatcher
{
    /**
     * @var CurrentListenerSniffCodeProvider
     */
    private $currentListenerSniffCodeProvider;

    public function __construct(CurrentListenerSniffCodeProvider $currentListenerSniffCodeProvider)
    {
        $this->currentListenerSniffCodeProvider = $currentListenerSniffCodeProvider;
    }

    /**
     * {@inheritdoc}
     */
    protected function doDispatch($listeners, $eventName, Event $event)
    {
        foreach ($listeners as $listener) {
            if ($event->isPropagationStopped()) {
                break;
            }

            $this->currentListenerSniffCodeProvider->setCurrentListener($listener);
            call_user_func($listener, $event, $eventName, $this);
        }
    }
}
