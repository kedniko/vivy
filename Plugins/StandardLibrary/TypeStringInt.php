<?php

namespace Kedniko\Vivy\Plugins\StandardLibrary;

use Kedniko\Vivy\Contracts\ContextInterface;
use Kedniko\Vivy\Core\Helpers;
use Kedniko\Vivy\Core\Options;
use Kedniko\Vivy\Messages\RuleMessage;
use Kedniko\Vivy\Messages\TransformerMessage;
use Kedniko\Vivy\Support\TypeProxy;
use Kedniko\Vivy\V;

final class TypeStringInt extends TypeStringNumber
{
    public function min($min, Options $options = null)
    {
        $options = Options::build($options, func_get_args());
        $errormessage = $options->getErrorMessage() ?: RuleMessage::getErrorMessage('string.min');
        $this->addRule(V::rule('min', fn (ContextInterface $c): bool => (int) $c->value >= $min, $errormessage), $options);

        return $this;
    }

    public function max($max, Options $options = null)
    {
        $options = Options::build($options, func_get_args());
        $errormessage = $options->getErrorMessage() ?: RuleMessage::getErrorMessage('string.max');
        $this->addRule(V::rule('max', fn (ContextInterface $c): bool => (int) $c->value <= $max, $errormessage), $options);

        return $this;
    }

    public function toInteger(Options $options = null)
    {
        $options = Helpers::getOptions($options);
        $options->setArgs(func_get_args());
        $errormessage = $options->getErrorMessage() ?: TransformerMessage::getErrorMessage('stringToInt');

        if (! (new TypeProxy($this))->hasRule('intString')) {
            $this->addRule(Rules::intString($options->getErrorMessage()), $options);
        }

        $transformer = Transformers::stringToInt($errormessage);
        $this->addTransformer($transformer, $options);

        return (new TypeInt())->from($this);
    }
}
