<?php

namespace Kedniko\Vivy\Support;

class Str
{
    /**
     * Determine if a given string contains a given substring.
     *
     * @param  string  $haystack
     * @param  string|string[]  $needles
     * @param  bool  $ignoreCase
     * @return bool
     */
    public static function contains($haystack, $needles, $ignoreCase = false)
    {
        if ($ignoreCase) {
            $haystack = mb_strtolower($haystack);
            $needles = array_map('mb_strtolower', (array) $needles);
        }

        foreach ((array) $needles as $needle) {
            if ($needle !== '' && strpos($haystack, $needle)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Determine if a given string starts with a given substring.
     *
     * @param  string  $haystack
     * @param  string|string[]  $needles
     * @return bool
     */
    public static function startsWith($haystack, $needles, $ignoreCase = true)
    {
        foreach ((array) $needles as $needle) {
            if ($needle !== '' && substr($haystack, 0, strlen($needle)) === $needle) {
                return true;
            }
        }

        return false;
    }

    /**
     * Determine if a given string ends with a given substring.
     *
     * @param  string  $haystack
     * @param  string|string[]  $needles
     * @return bool
     */
    public static function endsWith($haystack, $needles, $ignoreCase = true)
    {
        if ($ignoreCase) {
            $haystack = mb_strtolower($haystack);
        }
        foreach ((array) $needles as $needle) {
            if ($ignoreCase) {
                $needle = mb_strtolower($needle);
            }
            if (
                (string) $needle !== '' && substr($haystack, -strlen($needle)) === $needle
            ) {
                return true;
            }
        }

        return false;
    }
}
