<?php

namespace Kedniko\Vivy\Core;

final class Undefined
{
    private static ?\Kedniko\Vivy\Core\Undefined $instance = null;

    private function __construct()
    {
    }

    public static function instance(): Undefined
    {
        if (! isset(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }
}
