<?php

namespace Kedniko\Vivy\Core;

final class Event
{
    private static array $eventListeners = [];

    public static function listen($name, $callable): void
    {
        self::$eventListeners[$name][] = $callable;
    }

    public static function dispatch($name, $payload = []): void
    {
        if (! isset(self::$eventListeners[$name])) {
            return;
        }

        $callbacks = self::$eventListeners[$name];
        foreach ($callbacks as $cb) {
            $cb($payload);
        }
    }
}
