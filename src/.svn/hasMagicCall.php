<?php

/**
 * This file is inside .svn folder so it is ignored by php intellisense
 * include
 */

namespace Kedniko\Vivy\Call;

use Kedniko\Vivy\Support\Util;

trait hasMagicCall
{
	public function __call($methodName, $args)
	{
		$callerClass = static::class;
		$callerObj = $this;
		return Util::handleUserDefinedCall($callerClass, $methodName, $callerObj, $args);
		// return Util::handleUserDefinedCall(get_class($this), $methodName, $args);
	}
}
