<?php

namespace Kedniko\Vivy;

use Kedniko\Vivy\Traits\Typeable;
use Kedniko\Vivy\Contracts\TypeInterface;
use Kedniko\Vivy\Rules;
use Kedniko\Vivy\Core\Options;
use Kedniko\Vivy\Support\Util;
use Kedniko\Vivy\Messages\RuleMessage;

class Type implements TypeInterface
{
    use Typeable;

    public function equals(mixed $value, bool $strict = true, Options $options = null)
    {
        $options = Options::build($options, Util::getRuleArgs(__METHOD__, func_get_args()), __METHOD__);
        $errormessage = $options->getErrorMessage() ?: RuleMessage::getErrorMessage('valueNotAllowed');
        $this->addRule(Rules::equals($value, $strict, $errormessage), $options);

        return $this;
    }

    public function notEquals(mixed $value, bool $strict = true, Options $options = null)
    {
        $options = Options::build($options, Util::getRuleArgs(__METHOD__, func_get_args()), __METHOD__);
        $errormessage = $options->getErrorMessage() ?: RuleMessage::getErrorMessage('valueNotAllowed');
        $this->addRule(Rules::notEquals($value, $strict, $errormessage), $options);

        return $this;
    }
}
