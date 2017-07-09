<?php

namespace Emanci\ConsoleColor;

class Style
{
    /**
     * The current foreground color.
     *
     * @var int
     */
    protected $foreground;

    /**
     * The current background color.
     *
     * @var int
     */
    protected $background;

    /**
     * The current format style.
     *
     * @var array
     */
    protected $formats = [];

    /**
     * The default foreground color.
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
     * The default background color.
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
     * The default format.
     *
     * @var array
     */
    protected $defaultFormat = [
        'bold' => 1,
        'dim' => 2,
        'underline' => 4,
        'blink' => 5,
        'invert' => 7,
        'hidden' => 8,
    ];

    /**
     * Apply the string.
     *
     * @param string $str
     *
     * @return string
     */
    public function applyStyle($str, $line = true)
    {
        $attrs = array_merge(
            (array) $this->foreground,
            (array) $this->background,
            $this->formats
        );

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
        sort($attrs);

        return implode(';', $attrs);
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

        throw new StyleNotFoundException("Invalid style {$name}.");
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
        return $this->searchStyle($format, $this->defaultFormat);
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
}
