<?php

namespace App\Types;

use Kedniko\Vivy\Contracts\ContextInterface;
use Kedniko\Vivy\Core\Options;
use Kedniko\Vivy\Plugin\Standard\TypeString;
use Kedniko\Vivy\Support\Str;
use Kedniko\Vivy\V;

class TypePhone extends TypeString
{
    public static function phone(Options $options = null)
    {
        $type = new TypePhone();
        $type->addRule(V::rule('phone', function (ContextInterface $c) {
            return is_string($c->value);
        }), $options);

        return $type;
    }

    public function toPL(Options $options = null)
    {
        $this->addTransformer(V::transformer('toPL', function (ContextInterface $c) {
            return '+48 ' . $c->value;
        }), $options);

        return $this;
    }

    public function toIT(Options $options = null)
    {
        $this->addTransformer(V::transformer('toIT', function (ContextInterface $c) {
            return '+39 ' . $c->value;
        }), $options);

        return $this;
    }

    public function startsWith123(Options $options = null)
    {
        $this->addRule(V::rule('startsWith123', function (ContextInterface $c) {
            return Str::startsWith($c->value, '123');
        }), $options);

        return $this;
    }

    public function toUS(Options $options = null)
    {
        $this->addTransformer(V::transformer('toUS', function (ContextInterface $c) {
            return '+1 ' . $c->value;
        }), $options);

        return $this;
    }
}
