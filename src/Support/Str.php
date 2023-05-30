<?php

namespace Kedniko\Vivy\Support;

final class Str
{
    /**
     * Determine if a given string contains a given substring.
     *
     * @param  string  $haystack
     * @param  string|string[]  $needles
     * @param  bool  $ignoreCase
     * @return bool
     */
    public static function contains($haystack, $needles, $ignoreCase = false): bool
    {
        if ($ignoreCase) {
            $haystack = mb_strtolower($haystack);
            $needles = array_map('mb_strtolower', (array) $needles);
        }

        foreach ((array) $needles as $needle) {
            if ($needle === '') {
                continue;
            }
            if (!strpos($haystack, $needle)) {
                continue;
            }
            return true;
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
    public static function startsWith($haystack, $needles, $ignoreCase = true): bool
    {
        foreach ((array) $needles as $needle) {
            if ($needle === '') {
                continue;
            }
            if (substr($haystack, 0, strlen($needle)) !== $needle) {
                continue;
            }
            return true;
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
    public static function endsWith($haystack, $needles, $ignoreCase = true): bool
    {
        if ($ignoreCase) {
            $haystack = mb_strtolower($haystack);
        }
        foreach ((array) $needles as $needle) {
            if ($ignoreCase) {
                $needle = mb_strtolower($needle);
            }
            if ((string) $needle === '') {
                continue;
            }
            if (substr($haystack, -strlen($needle)) !== $needle) {
                continue;
            }
            return true;
        }

        return false;
    }
}
