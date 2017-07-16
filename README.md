## console-color

A simple command line colorize library

## Usage

```php
$consoleColor = new ConsoleColor();

// 字体色渲染
$consoleColor->red('红色字体');
$consoleColor->red()->render('红色字体');
$consoleColor->blue('蓝色字体');
$consoleColor->blue()->render('蓝色字体');

// 背景色渲染
$consoleColor->redBackground('红色背景');
$consoleColor->redBackground()->render('红色背景');
$consoleColor->blueBackground('蓝色背景');
$consoleColor->blueBackground()->render('蓝色背景');

// 控制序列渲染
$consoleColor->bold('粗体文字');
$consoleColor->underline('带下划线的文字');
$consoleColor->underline()->render('带下划线的文字');

// 内置主题渲染
$consoleColor->info('输出提示信息');
$consoleColor->error('输出错误信息');
$consoleColor->warning('输出警告信息');
$consoleColor->success('输出成功信息');

// 添加自定义颜色
$consoleColor->addColor('fooBar', 94);              // 添加单个颜色
$consoleColor->fooBar('自定义颜色字体，单个属性');
$consoleColor->addColor('fooBar', [1, 4, 41, 92]);  // 添加单个颜色
$consoleColor->fooBar('自定义颜色字体，含多个属性');
$consoleColor->addColor(['foo' => [38, 5, 5, 48, 5, 3], 'bar' => [48, 5, 28]]);  // 添加多个颜色（256）
$consoleColor->foo('自定义颜色字体，含多个属性');
$consoleColor->foo()->bar('自定义颜色字体，含多个属性');

// 支持 88/256 Colors
$consoleColor->color256(12)->render('8/256 字体');             // 字体颜色
$consoleColor->color256(12, FOREGROUND)->render('8/256 字体'); // 字体颜色
$consoleColor->color256(25, BACKGROUND)->render('8/256 背景'); // 背景颜色

// 组合渲染
$consoleColor->blueBackground()->red('蓝色背景，红色字体');
$consoleColor->red()->blueBackground()->render('蓝色背景，红色字体，其他组合方式');
$consoleColor->blue()->yellowBackground()->red()->render('黄色背景，红色字体');
$consoleColor->color256(6)->yellowBackground()->render('8/256 字体，黄色背景');
$consoleColor->red()->color256(60, BACKGROUND)->render('8/256 背景，红色字体');
```

Example will output

<img src="https://github.com/emanci/console-color/blob/master/colors.png" width = "260" alt="example-output" align=center />

#### Colors/Formats Api

| Foreground    | Background              | Formats     |
|---------------|-------------------------|-------------|
| default       | defaultBackground       | bold
| black         | blackBackground         | dim
| red           | redBackground           | underline
| green         | greenBackground         | blink
| yellow        | yellowBackground        | invert
| blue          | blueBackground          | hidden
| magenta       | magentaBackground       |
| cyan          | cyanBackground          |
| lightGray     | lightGrayBackground     |
| darkGray      | darkGrayBackground      |
| lightRed      | lightRedBackground      |
| lightGreen    | lightGreenBackground    |
| lightYellow   | lightYellowBackground   |
| lightBlue     | lightBlueBackground     |
| lightMagenta  | lightMagentaBackground  |
| lightCyan     | lightCyanBackground     |
| white         | whiteBackground         |

## Reference

* [bash:tip_colors_and_formatting](http://misc.flogisoft.com/bash/tip_colors_and_formatting#colors2)
* [Wiki-Bash/Prompt_customization#Colors](https://wiki.archlinux.org/index.php/Bash/Prompt_customization#Colors)
* [Wiki-ANSI_escape_code#Colors](https://en.wikipedia.org/wiki/ANSI_escape_code#Colors)

## License

Licensed under the [MIT license](https://github.com/emanci/console-color/blob/master/LICENSE).
