<?php

namespace App\Types;

use Kedniko\Vivy\Context;
use Kedniko\Vivy\Core\Options;
use Kedniko\Vivy\Interfaces\VivyPlugin;
use Kedniko\Vivy\Types\Type;
use Kedniko\Vivy\V;

class TypeToken extends Type implements VivyPlugin
{
    public function register()
    {
        V::register([V::class, TypeAny::class], 'token', [self::class, 'token'], self::class);
    }

    public function token(Options $options = null)
    {
        $type = new TypeToken();
        $type->addRule(V::rule('token', fn (Context $c) => $c->value instanceof \App\Token, fn (Context $c) => "$c->value is not a valid token!"));

        return $type;
    }

    public function notExpired(Options $options = null)
    {
        $options = Options::build($options, func_get_args());
        // $errormessage = $options->getErrormessage() ?: RuleMessage::getErrorMessage('string.min');
        $this->addRule(V::rule('notExpired', function (Context $c) {
            /** @var \App\Token */
            $value = $c->value;

            return $value->expired === false;
        }, function (Context $c) use ($options) {
            return 'Il token è scaduto'.$options->getErrorMessage();
        }), $options);

        return $this;
    }
}
