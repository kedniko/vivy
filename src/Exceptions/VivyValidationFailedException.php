<?php

namespace Kedniko\Vivy\Exceptions;

class VivyValidationFailedException extends VivyException
{
	private $payload;

	public function setPayload($payload)
	{
		$this->payload = $payload;
		return $this;
	}

	public function getPayload()
	{
		return $this->payload;
	}
}
