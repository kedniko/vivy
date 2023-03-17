<?php

namespace Kedniko\Vivy\Core;

use Kedniko\Vivy\Types\Type;

class Options
{
	protected $stopOnFailure;
	protected $stopOnSuccess;
	protected $errormessage;
	protected $errormessageDefault;

	/**
	 * @var bool
	 */
	protected $once;
	protected $if;
	protected $appendAfterCurrent;

	/**
	 * @var bool Optimization
	 */
	protected $withContext;

	protected $args;

	/**
	 * @var Type
	 */
	protected $builder;

	/**
	 * @param null|string $errormessage
	 * @param bool $stopOnFailure
	 */
	public function __construct()
	{
		$this->errormessage = null;
		$this->stopOnFailure = true;
		$this->stopOnSuccess = false;
		$this->once = false;
		$this->args = [];
		$this->if = Undefined::instance();
		$this->withContext = true;
	}

	public static function build(Options $options = null, array $args, string $errormessage = null)
	{
		if (!($options instanceof Options)) {
			$options = new Options();
		}
		$options->setArgs($args);
		$options->errormessageDefault = $errormessage; // TODO: not implemented
		return $options;
	}

	/**
	 * @param string|callable $errormessage
	 */
	public function message($errormessage)
	{
		$this->errormessage = $errormessage;
		return $this;
	}

	/**
	 * Do not pass context to the callback to improve performance
	 */
	public function ignoreContext()
	{
		$this->withContext = false;
		return $this;
	}

	/**
	 * Remove this rule after it has been used
	 */
	public function once()
	{
		$this->once = true;
		return $this;
	}

	public function appendAfterCurrent()
	{
		$this->appendAfterCurrent = true;
		return $this;
	}

	public function continueOnFailure()
	{
		$this->stopOnFailure = false;
		return $this;
	}

	// TODO
	public function stopOnSuccess()
	{
		$this->stopOnSuccess = true;
		return $this;
	}

	public function stopOnFailure()
	{
		$this->stopOnFailure = true;
		return $this;
	}

	// public function setStopOnFailure(bool $bool)
	// {
	// 	$this->stopOnFailure = $bool;
	// 	return $this;
	// }

	public function getArgs()
	{
		return $this->args;
	}

	/**
	 * @param array $args
	 */
	public function setArgs($args)
	{
		$args = array_filter($args, function ($arg) {
			return !($arg instanceof Options);
		});
		$this->args = $args;
		return $this;
	}

	/**
	 * @param array $args
	 */
	public function addArgs($args)
	{
		foreach ($args as $arg) {
			$this->args[] = $arg;
		}
		return $this;
	}

	/**
	 * Get the value of stopOnFailure
	 */
	public function getStopOnFailure()
	{
		return $this->stopOnFailure;
	}

	/**
	 * Get the value of errormessage
	 *
	 * @return  string
	 */
	public function getErrorMessage()
	{
		return $this->errormessage;
	}

	/**
	 * Get the value of consume
	 *
	 * @return  bool
	 */
	public function getOnce()
	{
		return $this->once;
	}

	/**
	 * Get the value of appendAfterCurrent
	 */
	public function getAppendAfterCurrent()
	{
		return $this->appendAfterCurrent;
	}

	public function hasIf()
	{
		return !($this->if instanceof Undefined);
	}

	/**
	 * Get the value of if
	 */
	public function getIf()
	{
		return $this->if;
	}

	/**
	 * Set the value of if
	 * @param callable $callback function(Context $context) {...}
	 * @return  self
	 */
	public function ifRule($if)
	{
		$this->if = $if;

		return $this;
	}

	// CHECKME
	public function getBuilder()
	{
		return $this->builder;
	}
}
