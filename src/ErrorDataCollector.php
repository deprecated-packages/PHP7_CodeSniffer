<?php

/*
 * This file is part of Symplify
 * Copyright (c) 2016 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\PHP7_CodeSniffer;

use Symplify\PHP7_CodeSniffer\EventDispatcher\CurrentListenerSniffCodeProvider;
use Symplify\PHP7_CodeSniffer\File\File;

final class ErrorDataCollector
{
    /**
     * @var int
     */
    private $errorCount = 0;

    /**
     * @var int
     */
    private $fixableErrorCount = 0;

    /**
     * @var array[]
     */
    private $errorMessages = [];

    /**
     * @var CurrentListenerSniffCodeProvider
     */
    private $currentListenerSniffCodeProvider;

    public function __construct(CurrentListenerSniffCodeProvider $currentListenerSniffCodeProvider)
    {
        $this->currentListenerSniffCodeProvider = $currentListenerSniffCodeProvider;
    }

    public function getErrorCount() : int
    {
        return $this->errorCount;
    }

    public function getFixableErrorCount() : int
    {
        return $this->fixableErrorCount;
    }

    public function getErrorMessages() : array
    {
        return $this->sortErrorMessagesByFileAndLine($this->errorMessages);
    }

    public function addErrorMessage(string $filePath, string $message, int $line, string $sniffCode, array $data, bool $isFixable)
    {
        $this->errorCount++;

        if ($isFixable) {
            $this->fixableErrorCount++;
        }

        $this->errorMessages[$filePath][] = [
            'line' => $line,
            'message' => $this->applyDataToMessage($message, $data),
            'sniffCode' => $this->getSniffFullCode($sniffCode),
            'isFixable'  => $isFixable
        ];
    }

    private function getSniffFullCode(string $sniffCode) : string
    {
        $parts = explode('.', $sniffCode);
        if ($parts[0] !== $sniffCode) {
            return $sniffCode;
        }

        $listenerSniffCode = $this->currentListenerSniffCodeProvider->getCurrentListenerSniffCode();
        return $listenerSniffCode.'.'.$sniffCode;
    }

    private function applyDataToMessage(string $message, array $data) : string
    {
        if (count($data)) {
            $message = vsprintf($message, $data);
        }

        return $message;
    }

    private function sortErrorMessagesByFileAndLine(array $errorMessages)
    {
        ksort($errorMessages);

        foreach ($errorMessages as $file => $errorMessagesForFile) {
            if (count($errorMessagesForFile) <= 1) {
                continue;
            }

            uasort($errorMessagesForFile, function ($first, $second) {
                return ($first['line'] > $second['line']);
            });

            $errorMessages[$file] = $errorMessagesForFile;
        }

        return $errorMessages;
    }
}
