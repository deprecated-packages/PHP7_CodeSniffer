<?php

/*
 * This file is part of Symplify
 * Copyright (c) 2016 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\PHP7_CodeSniffer\DI;

use Nette\DI\ContainerBuilder;
use Nette\DI\ServiceDefinition;

/**
 * @method ContainerBuilder getContainerBuilder()
 */
trait ExtensionHelperTrait
{
    private function addServicesToCollector(
        string $collectorClass,
        string $collectedClass,
        string $adderMethodName
    ) {
        $containerBuilder = $this->getContainerBuilder();
        $collectorDefinition = $this->getDefinitionByType($collectorClass);

        foreach ($containerBuilder->findByType($collectedClass) as $definition) {
            $collectorDefinition->addSetup(
                $adderMethodName,
                ['@'.$definition->getClass()]
            );
        }
    }

    private function getDefinitionByType(string $type) : ServiceDefinition
    {
        $containerBuilder = $this->getContainerBuilder();
        $definitionName = $containerBuilder->getByType($type);

        return $containerBuilder->getDefinition($definitionName);
    }
}
