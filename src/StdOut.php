<?php

namespace Emanci\ConsoleColor;

class StdOut implements OutputInterface
{
    /**
     * {@inheritdoc}
     */
    public function write($content, $newline = true)
    {
        if ($newline) {
            $content .= PHP_EOL;
        }

        fwrite(STDOUT, $content);
    }
}
