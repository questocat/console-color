<?php

/*
 * This file is part of questocat/console-color package.
 *
 * (c) questocat <zhengchaopu@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Questocat\ConsoleColor;

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
