<?php

namespace Emanci\ConsoleColor;

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
 * @method mixed underline()
 * @method mixed blink()
 * @method mixed invert()
 * @method mixed hidden()
 */

define('FOREGROUND', 38);
define('BACKGROUND', 48);

class ConsoleColor
{
    /**
     * The current foreground color.
     *
     * @var array
     */
    protected $foreground = [];

    /**
     * The current background color.
     *
     * @var array
     */
    protected $background = [];

    /**
     * The current format style.
     *
     * @var array
     */
    protected $formats = [];

    /**
     * The default foreground colors.
     *
     * @var array
     */
    protected $defaultForeground = [
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
    ];

    /**
     * The default background colors.
     *
     * @var array
     */
    protected $defaultBackground = [
        'default' => 49,
        'black' => 40,
        'red' => 41,
        'green' => 42,
        'yellow' => 43,
        'blue' => 44,
        'magenta' => 45,
        'cyan' => 46,
        'light_gray' => 47,
        'dark_gray' => 100,
        'light_red' => 101,
        'light_green' => 102,
        'light_yellow' => 103,
        'light_blue' => 104,
        'light_magenta' => 105,
        'light_cyan' => 106,
        'white' => 107,
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

    const COLORS_256_FOREGROUND = 38;
    const COLORS_256_BACKGROUND = 48;

    /**
     * @param string $method
     * @param array  $args
     *
     * @return $this
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
     * @param int $code
     * @param int $option
     *
     * @return string
     */
    public function color256($code, $option = null)
    {
        if (!$this->isSupportedColors256()) {
            return $this->warning('No supported colors 256.');
        }

        $option = $option ? $option : self::COLORS_256_FOREGROUND;
        $attrs = [$option, 5, $code];

        if ($option == self::COLORS_256_FOREGROUND) {
            $this->foreground = $attrs;
        } elseif ($option == self::COLORS_256_BACKGROUND) {
            $this->background = $attrs;
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

        if (strcmp(substr($name, -10), 'background') === 0) {
            $background = substr($name, 0, -11);
            if ($background = $this->searchBackground($background)) {
                $this->background = $background;

                return true;
            }
        } else {
            if ($foreground = $this->searchForeground($name)) {
                $this->foreground = $foreground;

                return true;
            } elseif ($format = $this->searchFormat($name)) {
                if (!array_key_exists($name, $this->formats)) {
                    $this->formats[] = $format;

                    return true;
                }
            }
        }

        return $this->error("Invalid style {$name}.");
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
     * Returns colorized string.
     *
     * @param string $str
     * @param bool   $line
     *
     * @return string
     */
    public function render($str = null, $line = true)
    {
        $attrs = array_merge(
            (array) $this->foreground,
            (array) $this->background,
            $this->formats
        );

        return $this->out($str, $attrs, $line);
    }

    /**
     * @param string $str
     * @param array  $attrs
     * @param bool   $line
     *
     * @return string
     */
    protected function out($str, array $attrs, $line = true)
    {
        return fwrite(STDERR, $this->applyStyle($str, $attrs, $line));
    }

    /**
     * Apply the string.
     *
     * @param string $str
     * @param array  $attrs
     *
     * @return string
     */
    protected function applyStyle($str, array $attrs, $line)
    {
        $this->resetStyle();
        $str = $this->colorize($str, $attrs);

        return $line ? $str."\n" : $str;
    }

    /**
     * Colorize the string.
     *
     * @param string $str
     * @param array  $attrs
     * @param bool   $end
     *
     * @return string
     */
    protected function colorize($str, $attrs, $end = true)
    {
        $start = $this->start($attrs);

        if ($end) {
            $end = $this->end();

            return $start.$str.$end;
        }

        return $start.$str;
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
     * Reset current style.
     */
    protected function resetStyle()
    {
        $this->foreground = null;
        $this->background = null;
        $this->formats = [];
    }

    /**
     * @param string $color
     *
     * @return string
     */
    protected function searchForeground($color)
    {
        return $this->searchStyle($color, $this->defaultForeground);
    }

    /**
     * @param string $color
     *
     * @return string
     */
    protected function searchBackground($color)
    {
        return $this->searchStyle($color, $this->defaultBackground);
    }

    /**
     * @param string $format
     *
     * @return string
     */
    protected function searchFormat($format)
    {
        return $this->searchStyle($format, $this->defaultFormats);
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
     * Add colors to the foreground colors.
     *
     * @param string $color
     * @param int    $code
     */
    public function addColor($color, $code = null)
    {
        $colors = $this->parseColor($color, $code);
        $mergeColors = array_merge($this->defaultForeground, $colors);
        $this->defaultForeground = $mergeColors;

        return $this;
    }

    /**
     * Add colors to the background colors.
     *
     * @param string $color
     * @param int    $code
     */
    public function addBackground($color, $code = null)
    {
        $colors = $this->parseColor($color, $code);
        $mergeColors = array_merge($this->defaultBackground, $colors);
        $this->defaultBackground = $mergeColors;

        return $this;
    }

    /**
     * Output info message.
     *
     * @param string $msg
     *
     * @return string
     */
    public function info($msg)
    {
        $infoTheme = $this->defaultThemes['info'];

        return $this->out($msg, $infoTheme);
    }

    /**
     * Output warning message.
     *
     * @param string $msg
     *
     * @return string
     */
    public function warning($msg)
    {
        $warningTheme = $this->defaultThemes['warning'];

        return $this->out($msg, $warningTheme);
    }

    /**
     * Output error message.
     *
     * @param string $msg
     *
     * @return string
     */
    public function error($msg)
    {
        $errorTheme = $this->defaultThemes['error'];

        return $this->out($msg, $errorTheme);
    }

    /**
     * Output success message.
     *
     * @param string $msg
     *
     * @return string
     */
    public function success($msg)
    {
        $successTheme = $this->defaultThemes['success'];

        return $this->out($msg, $successTheme);
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
        return is_array($color) ? $color : [$color => $code];
    }

    /**
     * @return bool
     */
    protected function isSupportedColors256()
    {
        return false !== strpos(getenv('TERM'), '256color');
    }
}
