<?php

/*
 * This file is part of Symplify
 * Copyright (c) 2016 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\PHP7_CodeSniffer\Event;

use Symfony\Component\EventDispatcher\Event;
use Symplify\PHP7_CodeSniffer\Contract\File\FileInterface;

final class CheckFileTokenEvent extends Event
{
    /**
     * @var FileInterface
     */
    private $file;

    /**
     * @var int
     */
    private $stackPointer;

    public function __construct(FileInterface $file, int $stackPointer)
    {
        $this->file = $file;
        $this->stackPointer = $stackPointer;
    }

    public function getFile() : FileInterface
    {
        return $this->file;
    }

    public function getStackPointer() : int
    {
        return $this->stackPointer;
    }
}
