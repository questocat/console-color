<?php

namespace Emanci\ConsoleColor;

interface OutputInterface
{
    /**
     * Writes a content to the output.
     *
     * @param string $content
     * @param bool   $newline
     *
     * @return mixed
     */
    public function write($content, $newline = false);
}
