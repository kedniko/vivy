<?php

namespace App;

use App\Types\TypePhone;
use App\Types\TypeToken;
use Kedniko\Vivy\Context;
use Kedniko\Vivy\Core\Options;
use Kedniko\Vivy\Interfaces\VivyPlugin;
use Kedniko\Vivy\V;

class Regole implements VivyPlugin
{
    public function register()
    {
        V::register([
            [V::class, 'phone', [TypePhone::class, 'phone'], TypePhone::class],
        ]);
    }

    public static function equals($value)
    {
        return V::rule('equals', function (Context $c) use ($value) {
            return $c->value === $value;
        }, function (Context $c) {
            return 'Non è uguale!';
        });
    }

    public static function notEquals($value)
    {
        return V::rule('notEquals', function (Context $c) use ($value) {
            return $c->value === $value;
        });
    }

    public static function pratica()
    {
        return V::rule('pratica', function (Context $c) {
            return $c->value instanceof TypeToken;
        }, function (Context $c) {
            return 'Non è una pratica ma '.gettype($c->value);
        });
    }

    public static function toIT(Options $options)
    {
        return V::transformer('toIT', function (Context $c) {
            return '+39 '.$c->value;
        });
    }
}
