<?php

/*
 * This file is part of Console Color.
 *
 * (c) emanci <zhengchaopu@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Emanci\ConsoleColor;

use InvalidArgumentException;

/*
 * @method mixed default()
 * @method mixed black()
 * @method mixed red()
 * @method mixed green()
 * @method mixed yellow()
 * @method mixed blue()
 * @method mixed magenta()
 * @method mixed cyan()
 * @method mixed lightGray()
 * @method mixed darkGray()
 * @method mixed lightRed()
 * @method mixed lightGreen()
 * @method mixed lightYellow()
 * @method mixed lightBlue()
 * @method mixed lightMagenta()
 * @method mixed lightCyan()
 * @method mixed white()
 * @method mixed defaultBackground()
 * @method mixed blackBackground()
 * @method mixed redBackground()
 * @method mixed Background()
 * @method mixed greenBackground()
 * @method mixed yellowBackground()
 * @method mixed blueBackground()
 * @method mixed magentaBackground()
 * @method mixed cyanlightGrayBackground()
 * @method mixed darkGrayBackground()
 * @method mixed lightRedBackground()
 * @method mixed lightGreenBackground()
 * @method mixed lightYellowBackground()
 * @method mixed lightBlueBackground()
 * @method mixed lightMagentaBackground()
 * @method mixed lightCyanBackground()
 * @method mixed whiteBackground()
 * @method mixed bold()
 * @method mixed dim()
 * @method mixed undernewline()
 * @method mixed blink()
 * @method mixed invert()
 * @method mixed hidden()
 */

define('FOREGROUND', 38);
define('BACKGROUND', 48);

class ConsoleColor
{
    const COLORS_256_FOREGROUND = 38;
    const COLORS_256_BACKGROUND = 48;

    /**
     * The current style.
     *
     * @var array
     */
    protected $current = [];

    /**
     * The output instance.
     *
     * @var OutputInterface
     */
    protected $output;

    /**
     * The default colors.
     *
     * @var array
     */
    protected $colors = [
        'default' => 39,
        'black' => 30,
        'red' => 31,
        'green' => 32,
        'yellow' => 33,
        'blue' => 34,
        'magenta' => 35,
        'cyan' => 36,
        'light_gray' => 37,
        'dark_gray' => 90,
        'light_red' => 91,
        'light_green' => 92,
        'light_yellow' => 93,
        'light_blue' => 94,
        'light_magenta' => 95,
        'light_cyan' => 96,
        'white' => 97,
        'default_background' => 49,
        'black_background' => 40,
        'red_background' => 41,
        'green_background' => 42,
        'yellow_background' => 43,
        'blue_background' => 44,
        'magenta_background' => 45,
        'cyan_background' => 46,
        'light_gray_background' => 47,
        'dark_gray_background' => 100,
        'light_red_background' => 101,
        'light_green_background' => 102,
        'light_yellow_background' => 103,
        'light_blue_background' => 104,
        'light_magenta_background' => 105,
        'light_cyan_background' => 106,
        'white_background' => 107,
    ];

    /**
     * The default formats.
     *
     * @var array
     */
    protected $defaultFormats = [
        'bold' => 1,
        'dim' => 2,
        'underline' => 4,
        'blink' => 5,
        'invert' => 7,
        'hidden' => 8,
    ];

    /**
     * The default themes.
     *
     * @var array
     */
    protected $defaultThemes = [
        'info' => [38, 5, 8],
        'warning' => [38, 5, 226],
        'error' => [38, 5, 196],
        'success' => [38, 5, 40],
    ];

    /**
     * ConsoleColor construct.
     *
     * @param OutputInterface|null $output
     */
    public function __construct(OutputInterface $output = null)
    {
        $this->output = $output;
    }

    /**
     * @param string $method
     * @param array  $args
     *
     * @return string|$this
     */
    public function __call($method, $args)
    {
        $str = implode('', $args);

        if ($this->styleWasCalled($method) && $str) {
            return $this->render($str);
        }

        return $this;
    }

    /**
     * @param string $name
     *
     * @throws StyleNotFoundException
     *
     * @return bool
     */
    public function styleWasCalled($name)
    {
        $name = $this->snakeCase($name);
        $supportedStyles = $this->getSupportedStyles();

        if ($code = $this->searchStyle($name, $supportedStyles)) {
            $this->mergeStyle($code);

            return true;
        }

        throw new StyleNotFoundException("Invalid style {$name}.");
    }

    /**
     * @param int $code
     * @param int $option
     *
     * @throws InvalidArgumentException
     *
     * @return $this
     */
    public function color256($code, $option = self::COLORS_256_FOREGROUND)
    {
        if (!$this->isSupportedColors256()) {
            throw new InvalidArgumentException('No supported colors 256.');
        }

        $attrs = [$option, 5, $code];
        $this->mergeStyle($attrs);

        return $this;
    }

    /**
     * Get the supported styles.
     *
     * @return array
     */
    protected function getSupportedStyles()
    {
        return array_merge(
            $this->colors,
            $this->defaultFormats,
            $this->defaultThemes
        );
    }

    /**
     * Merge the current style.
     *
     * @param mixed $code
     *
     * @return $this
     */
    protected function mergeStyle($code)
    {
        $code = is_array($code) ? $code : [$code];
        $this->current = array_merge($this->current, $code);

        return $this;
    }

    /**
     * Returns colorized string.
     *
     * @param string $str
     * @param bool   $newline
     *
     * @return string
     */
    public function render($str = null, $newline = true)
    {
        return $this->out($str, $newline);
    }

    /**
     * Set the output.
     *
     * @param OutputInterface $output
     *
     * @return $this
     */
    public function setOutput(OutputInterface $output)
    {
        $this->output = $output;

        return $this;
    }

    /**
     * Returns the output instance.
     *
     * @return OutputInterface
     */
    public function getOutput()
    {
        if (is_null($this->output)) {
            $this->output = new StdOut();
        }

        return $this->output;
    }

    /**
     * Add color to the colors.
     *
     * @param string $color
     * @param int    $code
     */
    public function addColor($color, $code = null)
    {
        $colors = $this->parseColor($color, $code);
        $mergeColors = array_merge($this->colors, $colors);
        $this->colors = $mergeColors;

        return $this;
    }

    /**
     * Convert a string to snake case.
     *
     * @param string $value
     * @param string $delimiter
     *
     * @return string
     */
    protected function snakeCase($value, $delimiter = '_')
    {
        if (!ctype_lower($value)) {
            $value = preg_replace('/\s+/u', '', $value);
            $value = strtolower(preg_replace('/(.)(?=[A-Z])/u', '$1'.$delimiter, $value));
        }

        return $value;
    }

    /**
     * @param string $content
     * @param bool   $newline
     *
     * @return string
     */
    protected function out($content, $newline = true)
    {
        $content = $this->applyStyle($content, $this->current);

        return $this->getOutput()->write($content, $newline);
    }

    /**
     * Apply the string.
     *
     * @param string $str
     * @param array  $attrs
     *
     * @return string
     */
    protected function applyStyle($str, array $attrs)
    {
        $this->resetStyle();

        return $this->colorize($str, $attrs);
    }

    /**
     * Reset current style.
     *
     * @return $this
     */
    protected function resetStyle()
    {
        $this->current = [];

        return $this;
    }

    /**
     * Colorize the string.
     *
     * @param string $str
     * @param array  $attrs
     *
     * @return string
     */
    protected function colorize($str, $attrs)
    {
        $start = $this->start($attrs);

        return $start.$str.$this->end();
    }

    /**
     * Get the string that begins the style.
     *
     * @param array $attrs
     *
     * @return string
     */
    protected function start(array $attrs)
    {
        $attrs = $this->buildAttrs($attrs);

        return $this->escSequence($attrs);
    }

    /**
     * Get the string that ends the style.
     *
     * @return string
     */
    protected function end()
    {
        return $this->escSequence(0);
    }

    /**
     * @param string $attrs
     *
     * @return string
     */
    protected function escSequence($attrs)
    {
        return sprintf("\e[%sm", $attrs);
    }

    /**
     * Build the style attributes.
     *
     * @param array $attrs
     *
     * @return string
     */
    protected function buildAttrs(array $attrs)
    {
        return implode(';', $attrs);
    }

    /**
     * @param string     $name
     * @param array      $styles
     * @param mixed|null $default
     *
     * @return mixed
     */
    protected function searchStyle($name, $styles, $default = null)
    {
        if (array_key_exists($name, $styles)) {
            return $styles[$name];
        }

        return $default;
    }

    /**
     * Parse the color.
     *
     * @param string $color
     * @param int    $code
     *
     * @return array
     */
    protected function parseColor($color, $code)
    {
        $colors = is_array($color) ? $color : [$color => $code];
        $return = [];

        array_walk($colors, function ($code, $color) use (&$return) {
            $color = $this->snakeCase($color);
            $return[$color] = $code;
        });

        return $return;
    }

    /**
     * @return bool
     */
    protected function isSupportedColors256()
    {
        return false !== strpos(getenv('TERM'), '256color');
    }
}
