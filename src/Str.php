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
     * Makes a `uuid` from a string or makes a new uuid
     *
     * @param null|string $thing
     *
     * @uses \remove_accents
     * @uses \wp_generate_uuid4
     *
     * @return string
     */
    public static function uuid(?string $thing = null): string
    {
        if (! $thing && function_exists('\\wp_generate_uuid4')) {
            return \wp_generate_uuid4();
        }

        $cleaned = static::removeAccents(
            strtolower(preg_replace('/[\W]/', '', $thing))
        );

        if (mb_strlen($cleaned) < 32) {
            $cleaned = str_pad($cleaned, 32, md5($cleaned));
        }

        if (mb_strlen($cleaned) > 32) {
            $cleaned = mb_substr($cleaned, 0, 32);
        }

        $replacement = '${1}-${2}-${3}-${4}-${5}';

        $regexp = '/^([0-9a-z]{8})([0-9a-z]{4})([0-9a-z]{4})([0-9a-z]{4})([0-9a-z]{12})$/';

        return preg_replace($regexp, $replacement, $cleaned);
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
                "/[\W]/" => static function ($match) {
                    return ' ';
                },
                "/[A-Z]/" => static function ($match) {
                    return ' ' . $match[0];
                },
            ],
            trim($thing)
        );

        if (! $prepared) {
            return [];
        }

        $prepared = static::removeAccents($prepared);

        return array_filter(
            array_map('strtolower', explode(' ', $prepared))
        );
    }

    /**
     * Converts all accent characters to ASCII characters.
     *
     * @param string $thing
     * @uses \remove_accents
     * @return string
     */
    public static function removeAccents(string $thing): string
    {
        return function_exists('\\remove_accents')
            ? \remove_accents($thing)
            : $thing;
    }
}
