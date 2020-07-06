<?php

namespace Rumur\WordPress\Notice;

class Str
{
    /**
     * Makes a `kebab-case` from a string.
     *
     * @param string $thing
     * @return string
     */
    public static function kebab(string $thing): string
    {
        return implode('-', static::parse($thing));
    }

    /**
     * Makes a `snake_case` from a string.
     *
     * @param string $thing
     * @return string
     */
    public static function snake(string $thing): string
    {
        return implode('_', static::parse($thing));
    }

    /**
     * Makes a `PascalCase` from a string.
     *
     * @param string $thing
     * @return string
     */
    public static function pascal(string $thing): string
    {
        return implode('', array_map('ucfirst', static::parse($thing)));
    }

    /**
     * Makes a `camelCase` from a string.
     *
     * @param string $thing
     * @return string
     */
    public static function camel(string $thing): string
    {
        return lcfirst(static::pascal($thing));
    }

    /**
     * Makes tokens from a string.
     *
     * @param string $thing
     *
     * @uses \remove_accents
     *
     * @return array
     */
    protected static function parse(string $thing): array
    {
        $prepared = preg_replace_callback_array(
            [
                '/[-_=~@\[\]()\"\\\']/i' => static function ($match) {
                    return ' ';
                },
                '/[A-Z]/' => static function ($match) {
                    return ' ' . $match[0];
                }
            ],
            trim($thing)
        );

        if (! $prepared) {
            return [];
        }

        if (function_exists('\\remove_accents')) {
            $prepared = \remove_accents($prepared);
        }

        return array_filter(
            array_map('strtolower', explode(' ', $prepared))
        );
    }
}
