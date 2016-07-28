<?php

/*
 * This file is part of Symplify
 * Copyright (c) 2016 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\PHP7_CodeSniffer\Configuration\EventSubscriber;

use Symfony\Component\Console\ConsoleEvents;
use Symfony\Component\Console\Event\ConsoleCommandEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symplify\PHP7_CodeSniffer\Configuration\Configuration;
use Symplify\PHP7_CodeSniffer\Console\Command\FixCommand;

final class ResolveConfigurationEventSubscriber implements EventSubscriberInterface
{
    /**
     * @var Configuration
     */
    private $configuration;

    public function __construct(Configuration $configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [ConsoleEvents::COMMAND => 'onConsoleRun'];
    }

    public function onConsoleRun(ConsoleCommandEvent $consoleCommandEvent)
    {
        $input = $consoleCommandEvent->getInput();

        $arguments = array_merge($input->getArguments(), $input->getOptions());
        $arguments = $this->detectFixerAndSetIt($consoleCommandEvent, $arguments);
        $arguments = $this->removeSystemArguments($arguments);

        $this->configuration->resolveFromArray($arguments);
    }

    private function detectFixerAndSetIt(ConsoleCommandEvent $consoleCommandEvent, array $arguments) : array
    {
        $command = $consoleCommandEvent->getCommand();
        if ($command instanceof FixCommand) {
            $arguments['isFixer'] = true;
            return $arguments;
        }

        return $arguments;
    }

    private function removeSystemArguments(array $arguments) : array
    {
        $systemArguments = ['help', 'command', 'version', 'command_name', 'format', 'raw'];
        foreach ($systemArguments as $systemArgument) {
            unset($arguments[$systemArgument]);
        }
        
        return $arguments;
    }
}
