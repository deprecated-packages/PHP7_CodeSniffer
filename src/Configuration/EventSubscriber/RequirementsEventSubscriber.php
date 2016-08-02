<?php

/*
 * This file is part of Symplify
 * Copyright (c) 2016 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\PHP7_CodeSniffer\Configuration\EventSubscriber;

use Symfony\Component\Console\ConsoleEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class RequirementsEventSubscriber implements EventSubscriberInterface
{
    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents() : array
    {
        return [
            ConsoleEvents::COMMAND => 'onConsoleRun'
        ];
    }

    public function onConsoleRun()
    {
        $this->ensureLineEndingsAreDetected();
        $this->setupVerbosityToMakeLegacyCodeRun();
    }

    /**
     * Ensure this option is enabled or else line endings will not always
     * be detected properly for files created on a Mac with the /r line ending.
     */
    private function ensureLineEndingsAreDetected()
    {
        ini_set('auto_detect_line_endings', true);
    }

    private function setupVerbosityToMakeLegacyCodeRun()
    {
        define('PHP_CODESNIFFER_VERBOSITY', 0);
    }
}
