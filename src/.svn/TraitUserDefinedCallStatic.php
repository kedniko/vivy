<?php

/**
 * This file is inside .git folder so it is ignored by php intellisense
 */

namespace Kedniko\Vivy\Call;

use Kedniko\Vivy\Support\Util;
use Kedniko\Vivy\Types\Type;

trait TraitUserDefinedCallStatic
{
	public static function __callStatic($methodName, $args)
	{
		$callerClass = static::class;
		$callerObj = null;
		return Util::handleUserDefinedCall($callerClass, $methodName, $callerObj, $args);
	}
}
