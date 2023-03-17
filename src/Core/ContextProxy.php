<?php

namespace Kedniko\Vivy\Core;

use Kedniko\Vivy\Context;

class ContextProxy extends Context
{
	/** @var Context $context */
	private $context;

	public $field;

	public function __construct($context)
	{
		$this->context = $context;
	}

	// public function valueRef()
	// {
	// 	$this->context->valueRef;
	// }
	// public function errorRef()
	// {
	// 	$this->context->errorsRef;
	// }
	// public function setValueProxy($index, $value)
	// {
	// 	$this->context->valueRef->setPath($index, $value);
	// }
	// public function setErrorProxy($index, $error)
	// {
	// 	$this->context->errorsRef->setPath($index, $error);
	// }

	/** @suppress(PHP0416) */
	public function setField($type)
	{
		$this->context->type = $type;
		return $this;
	}

	/** @suppress(PHP0416) */
	public function setIndex($value)
	{
		$this->context->index = $value;
		return $this;
	}

	/** @suppress(PHP0416) */
	public function setFailsCount($value)
	{
		$this->context->index = $value;
		return $this;
	}

	/** @suppress(PHP0416) */
	public function setSuccessCount($value)
	{
		$this->context->index = $value;
		return $this;
	}

	/** @suppress(PHP0416) */
	public function setExtra($index, $value)
	{
		$this->context->extra[$index] = $value;
		return $this;
	}

	/** @suppress(PHP0416) */
	public function getRawField()
	{
		return $this->context->type;
	}
}
