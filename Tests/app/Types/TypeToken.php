<?php

namespace App\Types;

use Kedniko\Vivy\Contracts\ContextInterface;
use Kedniko\Vivy\Core\Options;
use Kedniko\Vivy\Interfaces\VivyPlugin;
use Kedniko\Vivy\Support\Registrar;
use Kedniko\Vivy\Type;
use Kedniko\Vivy\V;

class TypeToken extends Type implements VivyPlugin
{
    public function register()
    {
        V::register(Registrar::make('token')->for([V::class, TypeAny::class])->callback([static::class, 'token'])->return(static::class));
    }

    public function token(Options $options = null)
    {
        $type = new TypeToken();
        $type->addRule(V::rule('token', fn (ContextInterface $c) => $c->value instanceof \App\Token, fn (ContextInterface $c) => "$c->value is not a valid token!"));

        return $type;
    }

    public function notExpired(Options $options = null)
    {
        $options = Options::build($options, func_get_args());
        // $errormessage = $options->getErrormessage() ?: RuleMessage::getErrorMessage('string.min');
        $this->addRule(V::rule('notExpired', function (ContextInterface $c) {
            /** @var \App\Token */
            $value = $c->value;

            return $value->expired === false;
        }, function (ContextInterface $c) use ($options) {
            return 'Il token è scaduto'.$options->getErrorMessage();
        }), $options);

        return $this;
    }
}
