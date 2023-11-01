<?php

/**
 * This file is inside .svn folder so it is ignored by php intellisense
 */

namespace Kedniko\Vivy\Call;

use Kedniko\Vivy\Support\Util;
use Kedniko\Vivy\Type;

trait hasMagicCallStatic
{
	public static function __callStatic($methodName, $args)
	{
		$callerClass = static::class;
		$callerObj = null;
		return Util::handleUserDefinedCall($callerClass, $methodName, $callerObj, $args);
	}
}
