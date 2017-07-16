<?php

/*
 * This file is part of ConsoleColor.
 *
 * (c) emanci <zhengchaopu@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

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
