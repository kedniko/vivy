<?php

namespace Kedniko\Vivy\Plugins\StandardLibrary;

use Kedniko\Vivy\Context;
use Kedniko\Vivy\Core\Helpers;
use Kedniko\Vivy\Core\Options;
use Kedniko\Vivy\Core\Transformers;
use Kedniko\Vivy\Messages\RuleMessage;
use Kedniko\Vivy\Messages\TransformerMessage;
use Kedniko\Vivy\Plugins\StandardLibrary\TypeInt;
use Kedniko\Vivy\Plugins\StandardLibrary\TypeString;
use Kedniko\Vivy\Rules;
use Kedniko\Vivy\TypesProxy\TypeProxy;
use Kedniko\Vivy\V;

class TypeStringInt extends TypeStringNumber
{
	public function min($min, Options $options = null)
	{
		$options = Options::build($options, func_get_args());
		$errormessage = $options->getErrorMessage() ?: RuleMessage::getErrorMessage('string.min');
		$this->addRule(V::rule('min', function (Context $c) use ($min) {
			return intval($c->value) >= $min;
		}, $errormessage), $options);
		return $this;
	}

	public function max($max, Options $options = null)
	{
		$options = Options::build($options, func_get_args());
		$errormessage = $options->getErrorMessage() ?: RuleMessage::getErrorMessage('string.max');
		$this->addRule(V::rule('max', function (Context $c) use ($max) {
			return intval($c->value) <= $max;
		}, $errormessage), $options);
		return $this;
	}

	public function toInteger(Options $options = null)
	{
		$options = Helpers::getOptions($options);
		$options->setArgs(func_get_args());
		$errormessage = $options->getErrorMessage() ?: TransformerMessage::getErrorMessage('stringToInt');

		if (!(new TypeProxy($this))->hasRule('intString')) {
			$this->addRule(Rules::intString($options->getErrorMessage()), $options);
		}

		$transformer = Transformers::stringToInt($errormessage);
		$this->addTransformer($transformer, $options);

		$type = (new TypeInt())->from($this);
		return $type;
	}
}
