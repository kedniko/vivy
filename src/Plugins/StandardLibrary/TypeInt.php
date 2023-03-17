<?php

namespace Kedniko\Vivy\Plugins\StandardLibrary;

use Kedniko\Vivy\Context;
use Kedniko\Vivy\Exceptions\VivyTransformerException;
use Kedniko\Vivy\Messages\TransformerMessage;
use Kedniko\Vivy\Plugins\StandardLibrary\TypeBool;
use Kedniko\Vivy\Plugins\StandardLibrary\TypeNumber;
use Kedniko\Vivy\Rules;
use Kedniko\Vivy\Transformer;

class TypeInt extends TypeNumber
{
	public function toBool($strict = true, $errormessage = null)
	{
		$errormessage = $errormessage ?: TransformerMessage::getErrorMessage('intToBool');

		$this->addRule(Rules::intBool($strict));

		$type = (new TypeBool())->from($this);

		$transformerID = 'intToBool';
		$errormessage = $errormessage ?: TransformerMessage::getErrorMessage($transformerID);
		$transformer = new Transformer($transformerID, function (Context $c) use ($strict) {
			$value = $c->value;

			if (!is_int($value)) {
				throw new VivyTransformerException();
			}

			if ($strict && !in_array($value, [0, 1], true)) {
				throw new VivyTransformerException();
			}

			if ($strict) {
				return $value === 1;
			} else {
				return boolval($value);
			}
		}, $errormessage);

		$type->addTransformer($transformer);

		return $type;
	}
}
