<?php

namespace Kedniko\Vivy\Core;

class Undefined
{
	private static $instance = null;

	private function __construct()
	{
	}

	public static function instance()
	{
		if (!isset(self::$instance)) {
			self::$instance = new self();
		}
		return self::$instance;
	}
}
