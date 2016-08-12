<?php

/*
 * This file is part of Symplify
 * Copyright (c) 2016 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\PHP7_CodeSniffer\DI;

use Nette\DI\CompilerExtension;
use Symfony\Component\Console\Command\Command;
use Symplify\PHP7_CodeSniffer\Configuration\ConfigurationResolver;
use Symplify\PHP7_CodeSniffer\Console\Php7CodeSnifferApplication;
use Symplify\PHP7_CodeSniffer\Contract\Configuration\OptionResolver\OptionResolverInterface;
use Symplify\PHP7_CodeSniffer\Contract\Ruleset\ToSniffNormalizer\ToSniffNormalizerInterface;
use Symplify\PHP7_CodeSniffer\Ruleset\Rule\ReferenceNormalizer;
use Symplify\PHP7_CodeSniffer\Ruleset\ToSniffNormalizer\RulesetXmlToSniffNormalizer;

final class Php7CodeSnifferExtension extends CompilerExtension
{
    use ExtensionHelperTrait;

    /**
     * {@inheritdoc}
     */
    public function loadConfiguration()
    {
        $this->loadServicesFromConfig();
    }

    /**
     * {@inheritdoc}
     */
    public function beforeCompile()
    {
        $this->loadToSniffNormalizersToReferenceNormalizer();
        $this->loadConsoleCommandsToConsoleApplication();
        $this->loadOptionResolversToConfigurationResolver();

        $this->injectReferenceNormalizer();
    }

    private function loadServicesFromConfig()
    {
        $config = $this->loadFromFile(__DIR__ . '/../config/services.neon');
        $this->compiler->parseServices($this->getContainerBuilder(), $config);
    }

    private function loadConsoleCommandsToConsoleApplication()
    {
        $this->addServicesToCollector(Php7CodeSnifferApplication::class, Command::class, 'add');
    }

    private function loadToSniffNormalizersToReferenceNormalizer()
    {
        $this->addServicesToCollector(
            ReferenceNormalizer::class,
            ToSniffNormalizerInterface::class,
            'addNormalizer'
        );
    }

    private function loadOptionResolversToConfigurationResolver()
    {
        $this->addServicesToCollector(
            ConfigurationResolver::class,
            OptionResolverInterface::class,
            'addOptionResolver'
        );
    }

    private function injectReferenceNormalizer()
    {
        $definition = $this->getDefinitionByType(RulesetXmlToSniffNormalizer::class);
        $definition->addSetup('setReferenceNormalizer', ['@' . ReferenceNormalizer::class]);
    }
}
