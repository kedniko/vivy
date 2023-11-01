<?php

namespace App;

use App\Types\TypePhone;
use App\Types\TypeToken;
use Kedniko\Vivy\Contracts\ContextInterface;
use Kedniko\Vivy\Core\Options;
use Kedniko\Vivy\Interfaces\VivyPlugin;
use Kedniko\Vivy\Support\Registrar;
use Kedniko\Vivy\V;

class Regole implements VivyPlugin
{
    public function register()
    {
        V::register(Registrar::make('phone')->for([V::class])->callback([TypePhone::class, 'phone'])->return(TypePhone::class));
    }

    public static function equals($value)
    {
        return V::rule('equals', function (ContextInterface $c) use ($value) {
            return $c->value === $value;
        }, function (ContextInterface $c) {
            return 'Non è uguale!';
        });
    }

    public static function notEquals($value)
    {
        return V::rule('notEquals', function (ContextInterface $c) use ($value) {
            return $c->value === $value;
        });
    }

    public static function pratica()
    {
        return V::rule('pratica', function (ContextInterface $c) {
            return $c->value instanceof TypeToken;
        }, function (ContextInterface $c) {
            return 'Non è una pratica ma '.gettype($c->value);
        });
    }

    public static function toIT(Options $options)
    {
        return V::transformer('toIT', function (ContextInterface $c) {
            return '+39 '.$c->value;
        });
    }
}
