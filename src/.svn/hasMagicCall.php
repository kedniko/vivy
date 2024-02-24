<?php

/**
 * This file is inside .svn folder so it is ignored by php intellisense
 */

namespace Kedniko\Vivy\Call;

use Kedniko\Vivy\Support\Util;

trait hasMagicCall
{
	public function __call($methodName, $args)
	{
		return Util::handleUserDefinedCall(
			className: static::class,
			methodName: $methodName,
			callerObj: $this,
			parameters: $args
		);
	}
}
