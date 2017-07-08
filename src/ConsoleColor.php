<?php
/**
 * @author: emanci <zhengchaopu@gmail.com>
 *
 * @copyright 2017 moerlong.com
 */

namespace Emanci\ConsoleColor;

class ConsoleColor
{
    /**
     * The style instance.
     *
     * @var Style
     */
    protected $style;

    /**
     * ConsoleColor construct.
     */
    public function __construct()
    {
        $this->style = new Style();
    }

    /**
     * @param string $method
     * @param array  $args
     *
     * @return $this
     */
    public function __call($method, $args)
    {
        $name = $this->snakeCase($method);
        $str = current($args);

        if ($this->style->styleWasCalled($name) && $str) {
            return $this->render($str);
        }

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
     * Returns colorized string.
     *
     * @param string $str
     * @param bool   $line
     *
     * @return string
     */
    public function render($str = null, $line = true)
    {
        return $this->style->applyStyle($str, $line);
    }
}
