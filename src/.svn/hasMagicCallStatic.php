<?php

/**
 * This file is inside .svn folder so it is ignored by php intellisense
 */

namespace Kedniko\Vivy\Call;

use Kedniko\Vivy\Support\Util;

trait hasMagicCallStatic
{
	public static function __callStatic($methodName, $args)
	{
		return Util::handleUserDefinedCall(
			className: static::class,
			methodName: $methodName,
			callerObj: null,
			parameters: $args
		);
	}
}
