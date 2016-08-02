<?php

/*
 * This file is part of Symplify
 * Copyright (c) 2016 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\PHP7_CodeSniffer\SniffFinder;

use Nette\Caching\Storages\DevNullStorage;
use Nette\Loaders\RobotLoader;
use ReflectionClass;
use Symfony\Component\Finder\Finder;
use Symplify\PHP7_CodeSniffer\SniffFinder\Composer\VendorDirProvider;
use Symplify\PHP7_CodeSniffer\SniffFinder\Naming\SniffNaming;

final class SniffFinder
{
    /**
     * @var string[]
     */
    private $sniffClassesPerDirectory = [];

    /**
     * {@inheritdoc}
     */
    public function findAllSniffClasses() : array
    {
        return $this->findAllSniffClassesInDirectory(VendorDirProvider::provide());
    }

    /**
     * {@inheritdoc}
     */
    public function findAllSniffs() : array
    {
        return $this->findSniffsInDirectory(VendorDirProvider::provide());
    }

    /**
     * {@inheritdoc}
     */
    public function findSniffsInDirectory(string $directory) : array
    {
        $filesInfo = (new Finder())->files()
            ->in($directory)
            ->name('*Sniff.php');

        return array_keys(iterator_to_array($filesInfo));
    }

    /**
     * {@inheritdoc}
     */
    public function findAllSniffClassesInDirectory(string $directory) : array
    {
        if (isset($this->sniffClassesPerDirectory[$directory])) {
            return $this->sniffClassesPerDirectory[$directory];
        }

        $robot = $this->createAndSetupRobotLoaderForDirectory($directory);
        $robot->rebuild();

        $sniffClasses = $this->filterOutAbstractAndNonPhpSniffs(array_keys($robot->getIndexedClasses()));
        $this->sniffClassesPerDirectory[$directory] = $sniffClasses;

        return $sniffClasses;
    }

    private function createAndSetupRobotLoaderForDirectory(string $directory) : RobotLoader
    {
        $robot = new RobotLoader();
        $robot->setCacheStorage(new DevNullStorage());
        $robot->addDirectory($directory);
        $robot->ignoreDirs .= ', tests, Tests';
        $robot->acceptFiles = '*Sniff.php';
        return $robot;
    }

    private function filterOutAbstractAndNonPhpSniffs(array $classes) : array
    {
        $sniffClasses = [];
        foreach ($classes as $class) {
            if ($this->isAbstractClass($class)) {
                continue;
            }

            if (!$this->doesSniffSupportsPhp($class)) {
                continue;
            }

            $code = SniffNaming::guessSniffCodeBySniffClass($class);
            $sniffClasses[$code] = $class;
        }

        return $sniffClasses;
    }

    private function isAbstractClass(string $className) : bool
    {
        return (new ReflectionClass($className))->isAbstract();
    }

    private function doesSniffSupportsPhp(string $className) : bool
    {
        $vars = get_class_vars($className);
        if (!isset($vars['supportedTokenizers'])) {
            return true;
        }

        if (in_array('PHP', $vars['supportedTokenizers'])) {
            return true;
        }

        return false;
    }
}
