<?php

/*
 * This file is part of Symplify
 * Copyright (c) 2016 Tomas Votruba (http://tomasvotruba.cz).
 */

namespace Symplify\PHP7_CodeSniffer;

use Symplify\PHP7_CodeSniffer\File\File;

final class Fixer
{
    /**
     * @var File
     */
    private $currentFile;

    /**
     * The list of tokens that make up the file contents.
     *
     * This is a simplified list which just contains the token content and nothing
     * else. This is the array that is updated as fixes are made, not the file's
     * token array. Imploding this array will give you the file content back.
     *
     * @var array<int, string>
     */
    private $tokens = array();

    /**
     * A list of tokens that have already been fixed.
     *
     * We don't allow the same token to be fixed more than once each time
     * through a file as this can easily cause conflicts between sniffs.
     *
     * @var int[]
     */
    private $fixedTokens = [];

    /**
     * A list of tokens that have been fixed during a changeset.
     *
     * @var array
     */
    private $changeset = [];

    /**
     * @var bool
     */
    private $inChangeset = false;

    public function startFile(File $file)
    {
        $this->currentFile = $file;
        $this->fixedTokens = [];

        $tokens = $file->getTokens();

        $this->tokens = [];
        foreach ($tokens as $index => $token) {
            if (isset($token['orig_content']) === true) {
                $this->tokens[$index] = $token['orig_content'];
            } else {
                $this->tokens[$index] = $token['content'];
            }
        }
    }

    public function getContents() : string
    {
        return implode($this->tokens);
    }

    public function getTokenContent(int $stackPtr) : string
    {
        if ($this->inChangeset === true
            && isset($this->changeset[$stackPtr]) === true
        ) {
            return $this->changeset[$stackPtr];
        } else {
            return $this->tokens[$stackPtr];
        }
    }

    public function beginChangeset()
    {
        $this->changeset = [];
        $this->inChangeset = true;
    }

    /**
     * @return bool|null
     */
    public function endChangeset()
    {
        $this->inChangeset = false;

        $success = true;
        $applied = [];

        foreach ($this->changeset as $stackPtr => $content) {
            $success = $this->replaceToken($stackPtr, $content);
            if ($success === false) {
                break;
            } else {
                $applied[] = $stackPtr;
            }
        }

        if ($success === false) {
            // Rolling back all changes.
            foreach ($applied as $stackPtr) {
                $this->revertToken($stackPtr);
            }
        }

        $this->changeset = [];
    }

    /**
     * Replace the entire contents of a token.
     *
     * @return bool If the change was accepted.
     */
    public function replaceToken(int $stackPtr, string $content) : bool
    {
        if ($this->inChangeset === false
            && isset($this->fixedTokens[$stackPtr]) === true
        ) {
            $indent = "\t";
            if (empty($this->changeset) === false) {
                $indent .= "\t";
            }

            return false;
        }

        if ($this->inChangeset === true) {
            $this->changeset[$stackPtr] = $content;
            return true;
        }

        $this->fixedTokens[$stackPtr] = $this->tokens[$stackPtr];
        $this->tokens[$stackPtr]      = $content;

        return true;
    }

    /**
     * @return bool If a change was reverted.
     */
    public function revertToken(int $stackPtr) : bool
    {
        if (isset($this->fixedTokens[$stackPtr]) === false) {
            return false;
        }

        $this->tokens[$stackPtr] = $this->fixedTokens[$stackPtr];
        unset($this->fixedTokens[$stackPtr]);

        return true;
    }

    public function substrToken(int $stackPtr, int $start, int $length = null) : bool
    {
        $current = $this->getTokenContent($stackPtr);

        if ($length === null) {
            $newContent = substr($current, $start);
        } else {
            $newContent = substr($current, $start, $length);
        }

        return $this->replaceToken($stackPtr, $newContent);
    }

    public function addNewline(int $stackPtr) : bool
    {
        $current = $this->getTokenContent($stackPtr);
        return $this->replaceToken($stackPtr, $current.$this->currentFile->eolChar);
    }

    public function addNewlineBefore(int $stackPtr) : bool
    {
        $current = $this->getTokenContent($stackPtr);
        return $this->replaceToken($stackPtr, $this->currentFile->eolChar.$current);
    }

    public function addContent(int $stackPtr, string $content) : bool
    {
        $current = $this->getTokenContent($stackPtr);
        return $this->replaceToken($stackPtr, $current.$content);
    }

    public function addContentBefore(int $stackPtr, string $content) : bool
    {
        $current = $this->getTokenContent($stackPtr);
        return $this->replaceToken($stackPtr, $content.$current);
    }
}
