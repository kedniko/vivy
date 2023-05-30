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
     */
    public static function contains($haystack, string|array $needles, $ignoreCase = false): bool
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
     */
    public static function startsWith($haystack, string|array $needles, $ignoreCase = true): bool
    {
        foreach ((array) $needles as $needle) {
            if ($needle === '') {
                continue;
            }
            if (!str_starts_with($haystack, $needle)) {
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
     */
    public static function endsWith($haystack, string|array $needles, $ignoreCase = true): bool
    {
        if ($ignoreCase) {
            $haystack = mb_strtolower($haystack);
        }
        foreach ((array) $needles as $needle) {
            if ($ignoreCase) {
                $needle = mb_strtolower($needle);
            }
            if ($needle === '') {
                continue;
            }
            if (!str_ends_with($haystack, $needle)) {
                continue;
            }
            return true;
        }

        return false;
    }
}
