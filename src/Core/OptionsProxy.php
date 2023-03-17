<?php

namespace Kedniko\Vivy\Core;

class OptionsProxy extends Options
{
	public function __construct($builder)
	{
		$this->builder = $builder;
	}

	public function setBuilder($builder)
	{
		$this->builder = $builder;
	}
}
