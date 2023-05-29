<?php

namespace Kedniko\Vivy\Plugins\StandardLibrary;

use Kedniko\Vivy\Core\Helpers;
use Kedniko\Vivy\Core\Options;
use Kedniko\Vivy\Core\Transformers;
use Kedniko\Vivy\Messages\TransformerMessage;
use Kedniko\Vivy\Rules;
use Kedniko\Vivy\TypesProxy\TypeProxy;

class TypeStringFloat extends TypeStringNumber
{
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

        $type = (new TypeInt())->from($this);

        return $type;
    }
}
