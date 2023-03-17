<?php

namespace Kedniko\Vivy\Core;

use Kedniko\Vivy\Context;
use Kedniko\Vivy\Rules;
use Kedniko\Vivy\Types\Type;
use Kedniko\Vivy\TypesProxy\TypeProxy;

class OrContext extends Context
{
	protected $childErrors;

	public function __construct($childErrors, Context $c = null)
	{
		parent::__construct($c);
		$this->childErrors = $childErrors;
	}

	public function childErrors()
	{
		return $this->childErrors;
	}
}
