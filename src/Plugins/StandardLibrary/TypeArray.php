<?php

namespace Kedniko\Vivy\Plugins\StandardLibrary;

use Kedniko\Vivy\ArrayContext;
use Kedniko\Vivy\Context;
use Kedniko\Vivy\Core\ContextProxy;
use Kedniko\Vivy\Core\Helpers;
use Kedniko\Vivy\Core\Options;
use Kedniko\Vivy\Core\Rule;
use Kedniko\Vivy\Core\Validated;
use Kedniko\Vivy\Plugins\StandardLibrary\TypeCompound;
use Kedniko\Vivy\Rules;
use Kedniko\Vivy\Transformer;
use Kedniko\Vivy\Types\Type;
use Kedniko\Vivy\V;

class TypeArray extends TypeCompound
{
	public function count($count, Options $options = null)
	{
		$options = Options::build($options, func_get_args());
		$errormessage = $options->getErrorMessage() ?: 'Numero di elementi non ammesso';

		$middleware = new Rule('count', function (Context $c) use ($count) {
			if (!is_array($c->value)) {
				return false;
			}
			return count($c->value) === $count;
		}, $errormessage);

		$this->addRule($middleware, $options);
		return $this;
	}

	public function minCount($minCount, Options $options = null)
	{
		$options = Options::build($options, func_get_args());
		$errormessage = $options->getErrorMessage() ?: 'Numero di elementi troppo piccolo';

		$middleware = new Rule('minCount', function (Context $c) use ($minCount) {
			if (!is_array($c->value)) {
				return false;
			}
			return count($c->value) >= $minCount;
		}, $errormessage);

		$this->addRule($middleware, $options);
		return $this;
	}

	public function maxCount($maxCount, Options $options = null)
	{
		$options = Options::build($options, func_get_args());
		$errormessage = $options->getErrorMessage() ?: 'Numero di elementi troppo grande';
		$middleware = new Rule('maxCount', function (Context $c) use ($maxCount) {
			if (!is_array($c->value)) {
				return false;
			}
			return count($c->value) <= $maxCount;
		}, $errormessage);

		$this->addRule($middleware, $options);
		return $this;
	}

	/**
	 * @param array $args
	 */
	public function toJson(Options $options = null)
	{
		$errormessage = $options->getErrorMessage() ?: 'TRANSFORMER: toJson';
		$transformer = new Transformer('toJson', function (Context $c) {
			return json_encode($c->value);
		}, $errormessage);
		$this->addTransformer($transformer, $options);
		return $this;
	}

	/**
	 * @param Type|array $type
	 * @param bool|callable $stopOnItemFailure
	 * @param Options|null $options
	 */
	public function each($type, $stopOnItemFailure = false, Options $options = null)
	{
		$options = Options::build($options, func_get_args());

		if (is_array($type)) {
			$type = V::group($type);
		}

		$rule = $this->getEachRule($type, $stopOnItemFailure, $options->getErrorMessage());
		$this->addRule($rule, $options);

		return $this;
	}

	private function getEachRule(Type $type, $stopOnItemFailure, $errormessage)
	{
		$ruleID = Rules::ID_EACH;
		$ruleFn = function (Context $c) use ($type, $stopOnItemFailure) {
			if (!is_array($c->value)) {
				throw new \Exception('This is not an array. Got [' . gettype($c->value) . ']: ' . json_encode($c->value), 1);
			}

			$contextProxy = new ContextProxy($c);

			$arrayContext = new ArrayContext();
			$arrayContextProxy = new ContextProxy($arrayContext);

			$failsCount = 0;
			$successCount = 0;

			// $time_start = microtime(true);

			foreach ($c->value as $index => $item) {
				$type->_extra = [
					'isArrayContext' => true,
					'index'          => $index,
					'failsCount'     => $failsCount,
				];

				$validated = $type->validate($item, $c);

				$c->value[$index] = $validated->value();

				if ($validated->fails()) {
					$failsCount++;
					$c->errors[$index] = $validated->errors();
					if (is_callable($stopOnItemFailure)) {
						$arrayContextProxy->setIndex($index);
						$arrayContextProxy->setFailsCount($failsCount);
						$arrayContextProxy->setSuccessCount($successCount);
						if ($stopOnItemFailure($arrayContext)) {
							break;
						}
					} elseif ($stopOnItemFailure) {
						break;
					}
				} else {
					$successCount++;
				}
			}

			// $time_end = microtime(true);
			// $milli = ($time_end - $time_start) * 1000;
			// echo "Execution time: {$milli} ms\n";
			// exit;

			return new Validated($c->value, $c->errors);
		};

		if ($errormessage === null) {
			$errormessage = function (Context $c) {
				return $c->errors;
			};
		}

		return new Rule($ruleID, $ruleFn, $errormessage);
	}

	// public function group($setup, Options $options = null)
	// {
	// 	$options = Helpers::getOptions($options);

	// 	$type = new BasicGroup($setup, $options);

	// 	$type->addRule(Rules::notNull($options->getErrormessage() ?: RuleMessage::getErrorMessage('group.notNull')), $options);
	// 	$type->addRule(Rules::notEmptyString($options->getErrormessage() ?: RuleMessage::getErrorMessage('group.notEmptyString')), $options);
	// 	$type->addRule(Rules::array($options->getErrormessage()), $options);

	// 	/** @var LinkedList $types */
	// 	$types = (new TypeProxy($type))->getChildState()->getFields();
	// 	$type->group($types, true, $options);

	// 	// share state
	// 	$type->state = $this->state;
	// 	return $type;
	// }
}
