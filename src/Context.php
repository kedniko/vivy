<?php

namespace Kedniko\Vivy;

use Kedniko\Vivy\Core\GroupContext;
use Kedniko\Vivy\Core\Ref;
use Kedniko\Vivy\Core\Undefined;
use Kedniko\Vivy\Types\Type;
use Kedniko\Vivy\TypesProxy\TypeProxy;

class Context
{
	// /** @var Ref $valueRef */
	// protected $valueRef;

	// /** @var Ref $errorsRef */
	// protected $errorsRef;

	/**
	 * @var mixed|array<mixed>
	 */
	public $value;

	public $errors;
	public array $childrenErrors;

	/** @var Context */
	public $rootContext;

	/** @var Context */
	public $fatherContext;

	// /** @var Context[] */
	// public $childrenContext;

	/** @var bool */
	private $isRootContext;

	/** @var Type */
	protected $type;

	/** @var array */
	public $args;

	public $fields;
	public $extra;

	public function __construct(Context $cloneFrom = null, Context $fatherContext = null, array $childrenContext = [])
	{
		if ($cloneFrom) {
			$this->value = $cloneFrom->value;
			$this->errors = $cloneFrom->errors;
			$this->args = $cloneFrom->args();
			// $this->errorsRef = $cloneFrom->errors; // TODO delete
			$this->rootContext = $cloneFrom->rootContext;
			$this->isRootContext = $cloneFrom->isRootContext();
		} else {
			$this->value = Undefined::instance();
			$this->errors = [];
			$this->args = [];
			// $this->errorsRef = [];
			$this->rootContext = null;
			$this->isRootContext = false;
		}

		if ($fatherContext) {
			$this->fatherContext = $fatherContext;
		}

		// if ($childrenContext) {
		// 	$this->childrenContext = $childrenContext;
		// }
	}

	/**
	 * Get the value of fatherContext
	 * @return Context
	 */
	public function fatherContext()
	{
		return $this->fatherContext;
	}

	/**
	 * Set the value of fatherContext
	 *
	 * @return self
	 */
	public function setFatherContext($fatherContext)
	{
		$this->fatherContext = $fatherContext;

		return $this;
	}

	/**
	 * Get the value of rootContext
	 * @return Context
	 */
	public function rootContext()
	{
		return $this->rootContext;
	}

	/**
	 * Set the value of rootContext
	 * @param Context|Undefined $rootContext
	 *
	 * @return self
	 */
	public function setRootContext($rootContext)
	{
		$this->rootContext = $rootContext;

		return $this;
	}

	/**
	 * Get the value of errors
	 */
	// public function errors()
	// {
	// 	return $this->errorsRef->value();
	// }

	/**
	 * Set the value of errors
	 *
	 * @return self
	 */
	public function setErrors($errors)
	{
		$this->errors = $errors;

		return $this;
	}

	/**
	 * Push error to errors array
	 *
	 * @return self
	 */
	// public function pushToErrors($error)
	// {
	// 	$this->errorsRef[] = $error;

	// 	return $this;
	// }

	/**
	 * Get the value of args
	 */
	public function args()
	{
		return $this->args;
	}

	public function isArrayContext()
	{
		return $this instanceof ArrayContext;
	}

	public function isGroupContext()
	{
		return $this instanceof GroupContext;
	}

	/**
	 * Set the value of args
	 * @param array $args
	 * @return self
	 */
	public function setArgs($args)
	{
		$this->args = $args;

		return $this;
	}

	/**
	 * Get the value of isRootContext
	 */
	public function isRootContext()
	{
		return $this->isRootContext;
	}

	/**
	 * Set the value of isRootContext
	 *
	 * @return self
	 */
	public function setIsRootContext($isRootContext)
	{
		$this->isRootContext = $isRootContext;

		return $this;
	}

	public function issetValue()
	{
		return !($this->value instanceof Undefined);
	}

	/**
	 * Unset the value of issetValue
	 */
	public function unsetValue()
	{
		$this->value = Undefined::instance();
		return $this;
	}

	public function isValid()
	{
		return !$this->errors;
	}

	public function getFieldContext(string $fieldname)
	{
		return $this->fields[$fieldname] ?? null;
	}

	/**
	 * Get the value of value
	 */
	// public function value()
	// {
	// 	// return $this->value instanceof Undefined ? null : $this->value;
	// 	return $this->valueRef->value();
	// }

	// /**
	//  * @param mixed $value
	//  */
	// public function setValue($value)
	// {
	// 	$this->value = $value;
	// 	return $this;
	// }

	public function getField()
	{
		$type = new \Kedniko\Vivy\Types\Type();

		// share state
		(new TypeProxy($type))->setChildState((new TypeProxy($this->type))->getState());

		return $type;
	}
}
