<?php

namespace Kedniko\Vivy\TypesProxy;

use Kedniko\Vivy\Context;
use Kedniko\Vivy\Core\LinkedList;
use Kedniko\Vivy\Core\Middleware;
use Kedniko\Vivy\Core\Options;
use Kedniko\Vivy\Core\Rule;
use Kedniko\Vivy\Core\Undefined;
use Kedniko\Vivy\Rules;
use Kedniko\Vivy\Support\Arr;
use Kedniko\Vivy\Types\Type;

final class TypeProxy extends Type
{
    /**
     * @param Type $type
     */
    public function __construct(public $type)
    {
    }

    public function setName($name)
    {
        return $this->type->state->setName($name);
    }

    public function getName(): string|Undefined
    {
        return $this->type->state->getName();
    }

    public function hasCustomErrorMessage($ruleID)
    {
        return isset($this->type->state->getCustomErrMessages()[$ruleID]);
    }

    public function getCustomErrorMessage($ruleID)
    {
        if (isset($this->type->state->getCustomErrMessages()[$ruleID])) {
            return $this->type->state->getCustomErrMessages()[$ruleID];
        }

        return null;
    }

    public function hasDefaultValue($ruleID): bool
    {
        $array = $this->type->state->getDefaultValues();

        return array_key_exists($ruleID, $array);
    }

    public function getDefaultValue($ruleID)
    {
        return $this->hasDefaultValue($ruleID) ? $this->type->state->getDefaultValues()[$ruleID] : null;
    }

    public function hasDefaultValueAny()
    {
        return $this->type->state->hasDefaultValuesAny();
    }

    public function getDefaultValueAny()
    {
        return $this->type->state->getDefaultValueAny();
    }

    public function hasErrorMessageEmpty()
    {
        return $this->type->state->hasErrorMessageEmpty();
    }

    public function getErrorMessageEmpty()
    {
        return $this->type->state->getErrorMessageEmpty();
    }

    public function hasErrorMessageAny()
    {
        return $this->type->state->hasErrorMessageAny();
    }

    public function getErrorMessageAny()
    {
        return $this->type->state->getErrorMessageAny();
    }

    public function hasValueIfOptionalNotExists()
    {
        return $this->type->state->hasValueIfOptionalNotExists();
    }

    public function getValueIfOptionalNotExists()
    {
        return $this->type->state->getValueIfOptionalNotExists();
    }

    /**
     * @return LinkedList
     */
    public function getMiddlewares()
    {
        // if($this->field instanceof BasicGroup){
        // 	return $this->field->groupBuilder->fields
        // }
        /** @var LinkedList $linkedList */
        $linkedList = $this->type->state->getMiddlewares();
        $linkedList->rewind();

        return $linkedList;
    }

    public function getMiddlewaresIds(): array
    {
        return $this->type->state->getMiddlewaresIds();
    }

    public function hasMiddlewares()
    {
        return $this->type->state->hasMiddlewares();
    }

    // public function getMiddleware($id)
    // {
    // 	// if($this->field instanceof BasicGroup){
    // 	// 	return $this->field->groupBuilder->fields
    // 	// }
    // 	/** @var LinkedList $linkedList */
    // 	$linkedList = $this->type->state->getMiddlewares();
    // 	$linkedList->rewind();
    // 	$linkedList->find(function (Middleware $item) {
    // 		return true;
    // 	});
    // 	return $linkedList;
    // }

    public function getRules(): ?array
    {
        return array_filter($this->type->state->getMiddlewares()->toArray(), fn($e): bool => $e instanceof Rule);
    }

    // public function isDefaultValueEnabled(){
    // 	return $this->field->state->getEnableDefaultValueIfOptional();
    // }
    // public function getDefaultValue(){
    // 	return $this->field->state->getDefaultValueIfOptional();
    // }
    public function getRule(mixed $ruleID): ?\Kedniko\Vivy\Core\Rule
    {
        /** @var LinkedList $middlewares */
        $middlewares = $this->type->state->getMiddlewares();

        $middlewares->rewind();
        while ($middlewares->hasNext()) {
            $middleware = $middlewares->getNext();
            if ($middleware instanceof Rule && $middleware->getID() === $ruleID) {
                $middlewares->rewind();
                return $middleware;
            }
        }
        $middlewares->rewind();

        return null;
    }

    public function hasRule($ruleID)
    {
        return isset($this->type->state->getMiddlewaresIds()[$ruleID]);
    }

    public function isRequired(Context $gc)
    {
        $state = $this->type->state;
        if (! ($state->requiredIf instanceof Undefined)) {
            $value = $state->requiredIf;
            if (is_callable($value)) {
                $value = $value($gc);
            }

            return (bool) $value;
        }

        if (! ($state->requiredIfField instanceof Undefined)) {
            $requiredIfField = $state->requiredIfField;
            $c = $requiredIfField['getContextFn']($gc);
            $value = $requiredIfField['value'];
            $value = is_callable($value) ? $value($c) : $c->value === $value;

            return (bool) $value;
        }

        return $state->isRequired();
    }

    /**
     * @param  bool|null  $stopOnFailure
     * @param  array|null  $args
     */
    public function prependRule(Rule $rule, Options $options = null)
    {
        // return parent::prependRule($rule, $options);

        $rule = $this->prepareRule($rule, $options);
        $this->type->prependMiddleware($rule);

        return $this;
    }

    // public function checkRequired($fieldname, $body)
    // {
    // 	$rule = $this->getRule(Rules::ID_REQUIRED);

    // 	if (!$rule) {
    // 		return;
    // 	}

    // 	$rule = $rule->getCallback();

    // 	if (!$rule || !is_callable($rule)) {
    // 		return;
    // 	}

    // 	return $rule($fieldname, $body);
    // }

    public function checkNotNull($fieldname, $body)
    {
        $rule = $this->getRule(Rules::ID_NOT_NULL);

        if (!$rule instanceof \Kedniko\Vivy\Core\Rule) {
            return;
        }

        $rule = $rule->getCallback();
        if (! $rule) {
            return;
        }
        if (! is_callable($rule)) {
            return;
        }

        return $rule($fieldname, $body);
    }

    public function checkNotEmptyString($fieldname, $body)
    {
        $rule = $this->getRule(Rules::ID_NOT_EMPTY_STRING);

        if (!$rule instanceof \Kedniko\Vivy\Core\Rule) {
            return;
        }

        $rule = $rule->getCallback();
        if (! $rule) {
            return;
        }
        if (! is_callable($rule)) {
            return;
        }

        return $rule($fieldname, $body);
    }

    public function canBeNull(): bool
    {
        return $this->type->state->canBeNull();
        // return !$this->hasRule(Rules::ID_NOT_NULL);
    }

    public function canBeEmptyString(): bool
    {
        return $this->type->state->canBeEmptyString();
        // return !$this->hasRule(Rules::ID_NOT_EMPTY_STRING);
    }

    // public function hasTransformCallback()
    // {
    // 	return property_exists($this, 'transformCallback') && is_array($this->transformCallback) && count($this->transformCallback) > 0;
    // }
    // public function getTransformCallbackArray()
    // {
    // 	return $this->transformCallback;
    // }
    /**
     * @param  string  $propertyName
     */
    public function setChildStateProperty($propertyName, mixed $value): void
    {
        $parts = explode('.', $propertyName);
        $propertyName = array_shift($parts);
        $path = implode('.', $parts);
        $this->type->state->{$propertyName} = Arr::set($this->type->state->{$propertyName}, $path, $value);

        // $this->type->state->{$propertyName} = $value;
        // Arr::set($this->field->state, $propertyName, $value);
    }

    public function setData($value): void
    {
        $this->type->state->setData($value);
    }

    public function setChildState($state): void
    {
        $this->type->state = $state;
    }

    public function getState()
    {
        return $this->type->state;
    }

    /**
     * Used in orProxy
     */
    public function getChildrenErrors()
    {
        return $this->type->state->_extra['or_errors'] ?? [];
    }
}
