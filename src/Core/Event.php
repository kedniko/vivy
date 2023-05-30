<?php

namespace Kedniko\Vivy\Core;

final class Event
{
    private static $eventListeners = [];

    public static function listen($name, $callable)
    {
        static::$eventListeners[$name][] = $callable;
    }

    public static function dispatch($name, $payload = [])
    {
        if (! isset(static::$eventListeners[$name])) {
            return;
        }

        $callbacks = static::$eventListeners[$name];
        foreach ($callbacks as $cb) {
            $cb($payload);
        }
    }
}
