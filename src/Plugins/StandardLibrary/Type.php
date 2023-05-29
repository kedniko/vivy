<?php

namespace Kedniko\Vivy\Plugins\StandardLibrary;

use Kedniko\Vivy\Core\Options;
use Kedniko\Vivy\Messages\RuleMessage;
use Kedniko\Vivy\Rules;

class Type extends \Kedniko\Vivy\Types\Type
{
    // public function string(Options $options = null)
    // {
    // 	$options = Options::build($options, func_get_args());
    // 	$type = new TypeString();
    // 	$type->state = $caller->state; // share state
    // 	$type->addRule(Rules::string($options->getErrormessage()), $options);
    // 	return $type;
    // }

    public function equals($value, $strict = true, Options $options = null)
    {
        $options = Options::build($options, func_get_args());
        $errormessage = $options->getErrorMessage() ?: RuleMessage::getErrorMessage('valueNotAllowed');
        $this->addRule(Rules::equals($value, $strict, $errormessage), $options);

        return $this;
    }

    public function notEquals($value, $strict = true, Options $options = null)
    {
        $options = Options::build($options, func_get_args());
        $errormessage = $options->getErrorMessage() ?: RuleMessage::getErrorMessage('valueNotAllowed');
        $this->addRule(Rules::notEquals($value, $strict, $errormessage), $options);

        return $this;
    }

    public function allowNull()
    {
        $this->removeRule(Rules::ID_NOT_NULL);
        $this->state->setNotNull(false);

        return $this;
    }
}
