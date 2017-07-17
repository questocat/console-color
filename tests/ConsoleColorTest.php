<?php

/*
 * This file is part of Console Color.
 *
 * (c) emanci <zhengchaopu@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Tests;

use Emanci\ConsoleColor\ConsoleColor;
use Emanci\ConsoleColor\OutputInterface;
use Mockery as m;

class ConsoleColorTest extends TestCase
{
    protected $consoleColor;

    protected $withoutSupportedColor256;

    protected $newConsoleColor;

    public function setUp()
    {
        $this->output = m::mock(OutputInterface::class);
        $this->output->shouldReceive('write')->once()->andReturnUsing(function ($content, $newline = true) {
            if ($newline) {
                $content .= PHP_EOL;
            }

            return $content;
        });
        $this->consoleColor = new ConsoleColorWithSupportedColor256($this->output);
        $this->consoleColor->setOutput($this->output);
        $this->withoutSupportedColor256 = new ConsoleColorWithoutSupportedColor256($this->output);
        $this->newConsoleColor = new ConsoleColor();
    }

    public function testColor256()
    {
        $this->assertEquals("\e[38;5;12m8/256 字体\e[0m".PHP_EOL, $this->consoleColor->color256(12)->render('8/256 字体'));
        $this->assertEquals("\e[38;5;12m8/256 字体\e[0m", $this->consoleColor->color256(12)->render('8/256 字体', false));
        $this->assertEquals("\e[48;5;35m8/256 背景\e[0m".PHP_EOL, $this->consoleColor->color256(35, BACKGROUND)->render('8/256 背景'));
    }

    public function testForeground()
    {
        $this->assertEquals("\e[31m红色字体\e[0m".PHP_EOL, $this->consoleColor->red('红色字体'));
        $this->assertEquals("\e[31m红色字体\e[0m".PHP_EOL, $this->consoleColor->red()->render('红色字体'));
    }

    public function testBackground()
    {
        $this->assertEquals("\e[44m蓝色背景\e[0m".PHP_EOL, $this->consoleColor->blueBackground('蓝色背景'));
        $this->assertEquals("\e[44m蓝色背景\e[0m".PHP_EOL, $this->consoleColor->blueBackground()->render('蓝色背景'));
    }

    public function testFormat()
    {
        $this->assertEquals("\e[4m带下划线的文字\e[0m".PHP_EOL, $this->consoleColor->underline('带下划线的文字'));
        $this->assertEquals("\e[4m带下划线的文字\e[0m".PHP_EOL, $this->consoleColor->underline()->render('带下划线的文字'));
        $this->assertEquals("\e[1m粗体文字\e[0m".PHP_EOL, $this->consoleColor->bold()->bold()->render('粗体文字'));
    }

    public function testTheme()
    {
        $this->assertEquals("\e[38;5;8m输出提示信息\e[0m".PHP_EOL, $this->consoleColor->info('输出提示信息'));
        $this->assertEquals("\e[38;5;196m输出错误信息\e[0m".PHP_EOL, $this->consoleColor->error('输出错误信息'));
        $this->assertEquals("\e[38;5;226m输出警告信息\e[0m".PHP_EOL, $this->consoleColor->warning('输出警告信息'));
        $this->assertEquals("\e[38;5;40m输出成功信息\e[0m".PHP_EOL, $this->consoleColor->success('输出成功信息'));
    }

    public function testAddColor()
    {
        $this->consoleColor->addColor('fooBar', 94);
        $this->assertEquals("\e[94m自定义颜色字体，单个属性\e[0m".PHP_EOL, $this->consoleColor->fooBar('自定义颜色字体，单个属性'));

        $this->consoleColor->addColor('fooBar', [1, 4, 41, 92]);
        $this->assertEquals("\e[1;4;41;92m自定义颜色字体，含多个属性\e[0m".PHP_EOL, $this->consoleColor->fooBar('自定义颜色字体，含多个属性'));

        $this->consoleColor->addColor(['foo' => [38, 5, 5, 48, 5, 3], 'bar' => [48, 5, 28]]);
        $this->assertEquals("\e[38;5;5;48;5;3m自定义颜色字体，含多个属性\e[0m".PHP_EOL, $this->consoleColor->foo('自定义颜色字体，含多个属性'));
        $this->assertEquals("\e[48;5;28m自定义颜色字体，含多个属性\e[0m".PHP_EOL, $this->consoleColor->bar('自定义颜色字体，含多个属性'));
    }

    /**
     * @expectedException        \Emanci\ConsoleColor\StyleNotFoundException
     * @expectedExceptionMessage Invalid style unknow.
     */
    public function testStyleNotFoundException()
    {
        $this->consoleColor->unknow('未知字体颜色');
    }

    public function testNoSupportedColor256Exception()
    {
        $this->setExpectedException('InvalidArgumentException', 'No supported colors 256.');
        $this->withoutSupportedColor256->color256(6)->render('8/256 字体');
    }

    public function testBlueForeground()
    {
        $this->newConsoleColor->blue('蓝色字体');
        $this->newConsoleColor->blue()->render('蓝色字体');
    }

    public function testIsSupportedColors256()
    {
        $this->newConsoleColor->color256(18)->render('8/256 字体');
    }
}

class ConsoleColorWithSupportedColor256 extends ConsoleColor
{
    protected function isSupportedColors256()
    {
        return true;
    }
}

class ConsoleColorWithoutSupportedColor256 extends ConsoleColor
{
    protected function isSupportedColors256()
    {
        return false;
    }
}
