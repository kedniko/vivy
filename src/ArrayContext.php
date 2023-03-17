<?php

namespace Kedniko\Vivy;

class ArrayContext extends Context
{
	public $index;
	public $failsCount;
	public $successCount;

	public function __construct(Context $c = null)
	{
		parent::__construct($c);
		$this->index = null;
		$this->failsCount = null;
		$this->successCount = null;
	}

	public function getIndex()
	{
		return $this->index;
	}

	public function getFailsCount()
	{
		return $this->failsCount;
	}

	public function getSuccessCount()
	{
		return $this->successCount;
	}
}
