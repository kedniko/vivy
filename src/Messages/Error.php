<?php

namespace Kedniko\Vivy\Messages;

use Closure;
use Kedniko\Vivy\Support\Arr;

class Error
{
    private static string $locale = 'en';

    private static array $paths = [];

    private static array $messages = [];

    private static Closure|string $defaultError;

    public static function addPath(string $path)
    {
        self::$paths[] = $path;
    }

    public static function setDefault(Closure|string $errormessage)
    {
        self::$defaultError = $errormessage;
    }

    public static function getDefaultError()
    {
        return self::$defaultError ?? null;
    }

    public static function setLocale(string $locale)
    {
        self::$locale = $locale;
    }


    public static function getLocale()
    {
        return self::$locale;
    }

    public static function get(string $key)
    {
        $locale = self::getLocale();
        foreach (self::$paths as $path) {
            $pc = explode('.', $key);
            $file = array_shift($pc);
            $arrkey = implode('.', $pc);

            $filename = "{$path}/{$locale}/{$file}.php";
            if (!file_exists($filename)) {
                continue;
            }

            if (!isset(self::$messages[$filename])) {
                self::$messages[$filename] = require $filename;
            }

            $err = Arr::get(self::$messages[$filename], $arrkey);
            if ($err) {
                return $err;
            }
        }
    }
}
