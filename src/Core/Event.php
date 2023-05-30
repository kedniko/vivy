<?php

namespace Kedniko\Vivy\Core;

final class Event
{
    private static array $eventListeners = [];

    public static function listen($name, $callable): void
    {
        static::$eventListeners[$name][] = $callable;
    }

    public static function dispatch($name, $payload = []): void
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
