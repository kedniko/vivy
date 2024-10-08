<?php

namespace Kedniko\Vivy\Core;

use Kedniko\Vivy\Contracts\ContextInterface;
use Kedniko\Vivy\Enum\RulesEnum;
use Kedniko\Vivy\Support\Arr;
use Kedniko\Vivy\Type;

// use Kedniko\Vivy\Contracts\ContextInterface;

final class Setup
{
    private $data;

    // public ContextInterface $context;

    private string|Undefined $name;

    private bool|Undefined $required;

    private bool|Undefined $notNull;

    private Rule $requiredRule;

    private readonly Rule $emptyStringRule;

    private Rule $notNullRule;

    private array $defaultValues = [];

    private $defaultValuesAny;

    private bool $enableDefaultValueIfOptional = false;

    private $defaultValueIfOptional;

    private array $customErrMessages = [];

    private \Closure|string|Undefined $errorMessageAny;

    private \Closure|string|Undefined $errorMessageEmpty;

    private \Closure|Undefined $valueIfOptionalNotExists;

    private LinkedList $middlewares;

    private array $middlewaresid = [];

    private array $onValid = [];

    private array $onError = [];

    private bool $stopOnFailure = false;

    private bool $once = false;

    public \Closure|Undefined $setupFn;

    public array $_extra = [];

    public LinkedList $fields;

    public \Closure|Undefined|bool $requiredIf;

    public array|Undefined $requiredIfField;

    public array $failHandlers;

    // /** @var array */
    // public $allow;
    // /** @var array */
    // public $deny;

    public function __construct()
    {
        $this->data = Undefined::instance();
        $this->name = Undefined::instance();
        $this->fields = new LinkedList();
        $this->defaultValuesAny = Undefined::instance();
        $this->errorMessageAny = Undefined::instance();
        $this->errorMessageEmpty = Undefined::instance();
        $this->valueIfOptionalNotExists = Undefined::instance();
        $this->middlewares = new LinkedList();

        $this->required = Undefined::instance();
        $this->requiredIf = Undefined::instance();
        $this->requiredIfField = Undefined::instance();
        $this->setupFn = Undefined::instance();
        $this->notNull = Undefined::instance();
        $this->defaultValueIfOptional = Undefined::instance();

        // $this->allow = [];
        // $this->deny = [];
    }

    public function hasData()
    {
        return ! $this->isUndefined($this->data);
    }

    public function getData()
    {
        return $this->data;
    }

    public function setData($data)
    {
        $this->data = $data;

        return $this;
    }

    public function isRequired(ContextInterface $gc)
    {
        if (! ($this->requiredIf instanceof Undefined)) {
            $value = $this->requiredIf;
            if (is_callable($value)) {
                $value = $value($gc);
            }

            return (bool) $value;
        }

        if (! ($this->requiredIfField instanceof Undefined)) {
            $requiredIfField = $this->requiredIfField;
            $c = $requiredIfField['getContextFn']($gc);
            $value = $requiredIfField['value'];
            $value = is_callable($value) ? $value($c) : $c->value === $value;

            return (bool) $value;
        }

        return $this->required === true;
    }

    public function getRequired(): bool|Undefined
    {
        return $this->required;
    }

    public function issetRequired()
    {
        return ! $this->isUndefined($this->required);
    }

    public function setRequired(bool|Undefined $required, ?Rule $rule = null)
    {
        $this->required = $required;
        if ($rule instanceof Rule) {
            $this->setRequiredRule($rule);
        }

        return $this;
    }

    // public function hasRequiredRule()
    // {
    // 	return !$this->isUndefined($this->requiredRule) && !is_null($this->requiredRule);
    // }

    public function getRequiredRule(): Rule
    {
        return $this->requiredRule;
    }

    public function getEmptyStringRule(): Rule
    {
        return $this->emptyStringRule;
    }

    public function getNotNullRule(): Rule
    {
        return $this->notNullRule;
    }

    public function setRequiredRule(Rule $rule)
    {
        $this->requiredRule = $rule;

        return $this;
    }

    public function setNotNullRule(Rule $rule)
    {
        $this->notNullRule = $rule;

        return $this;
    }

    public function removeRequiredRule()
    {
        $this->requiredRule = Undefined::instance();

        return $this;
    }

    public function canBeNull(): bool
    {
        if (! $this->hasNotNull()) {
            return false;
        }

        return $this->notNull === false;
    }

    public function hasNotNull()
    {
        return ! $this->isUndefined($this->notNull);
    }

    public function getNotNull(): bool|Undefined
    {
        return $this->notNull;
    }

    public function setNotNull(bool|Undefined $notNull)
    {
        $this->notNull = $notNull;

        return $this;
    }

    public function hasMiddlewares()
    {
        return ! $this->middlewares->isEmpty();
    }

    public function setMiddlewares(LinkedList $middlewares)
    {
        $this->middlewares = $middlewares;

        return $this;
    }

    public function getMiddlewaresIds(): array
    {
        return $this->middlewaresid;
    }

    public function hasMiddlewareId($id)
    {
        return isset($this->middlewaresid[$id]);
    }

    public function addMiddlewareId($middlewaresid)
    {
        if (! isset($this->middlewaresid[$middlewaresid])) {
            $this->middlewaresid[$middlewaresid] = 0;
        }
        $this->middlewaresid[$middlewaresid]++;

        return $this;
    }

    public function removeMiddlewareId($middlewaresid)
    {
        abs($this->middlewaresid[$middlewaresid]--);

        return $this;
    }

    public function hasValueIfOptionalNotExists()
    {
        return ! $this->isUndefined($this->valueIfOptionalNotExists);
    }

    public function getValueIfOptionalNotExists()
    {
        return $this->valueIfOptionalNotExists;
    }

    public function setValueIfOptionalNotExists(callable $value): self
    {
        $this->valueIfOptionalNotExists = $value;

        return $this;
    }

    public function hasName()
    {
        return ! $this->isUndefined($this->name);
    }

    public function getName(): string|Undefined
    {
        return $this->name;
    }

    public function setName(string|Undefined $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function hasStopOnFailure()
    {
        return ! $this->isUndefined($this->stopOnFailure);
    }

    public function getStopOnFailure(): bool
    {
        return $this->stopOnFailure;
    }

    public function setStopOnFailure(bool $stopOnFailure)
    {
        $this->stopOnFailure = $stopOnFailure;

        return $this;
    }

    public function getCustomErrMessages(): array
    {
        return $this->customErrMessages;
    }

    public function setCustomErrMessages(array $customErrMessages): self
    {
        $this->customErrMessages = $customErrMessages;

        return $this;
    }

    /**
     * @return LinkedList<Type>
     */
    public function getFields(): LinkedList
    {
        return $this->fields;
    }

    /**
     * Set the value of fields
     *
     * @param  LinkedList<Type>  $types
     */
    public function setFields(LinkedList $types): self
    {
        $this->fields = $types;

        return $this;
    }

    public function hasErrorMessageAny()
    {
        return ! $this->isUndefined($this->errorMessageAny);
    }

    /**
     * Get the value of errorMessageAny
     */
    public function getErrorMessageAny(): \Closure|string|Undefined
    {
        return $this->errorMessageAny;
    }

    public function setErrorMessageAny(\Closure|string|Undefined $errorMessageAny): self
    {
        $this->errorMessageAny = $errorMessageAny;

        return $this;
    }

    public function getOnValid(): array
    {
        return $this->onValid;
    }

    public function setOnValid(array $onValid): self
    {
        $this->onValid = $onValid;

        return $this;
    }

    public function addOnValid($onValid)
    {
        $this->onValid[] = $onValid;

        return $this;
    }

    public function getOnError(): array
    {
        return $this->onError;
    }

    public function setOnError(array $onError): self
    {
        $this->onError = $onError;

        return $this;
    }

    public function addOnError($onError, $ruleID = null)
    {
        if ($ruleID) {
            $this->onError['rules'][$ruleID][] = $onError;
        } else {
            $this->onError['all'][] = $onError;
        }

        return $this;
    }

    public function hasErrorMessageEmpty()
    {
        return ! $this->isUndefined($this->errorMessageEmpty);
    }

    /**
     * Get the value of errorMessageEmpty
     */
    public function getErrorMessageEmpty(): \Closure|string|Undefined
    {
        return $this->errorMessageEmpty;
    }

    /**
     * Set the value of errorMessageEmpty
     *
     * @return self
     */
    public function setErrorMessageEmpty(\Closure|string|Undefined $errorMessageEmpty)
    {
        $this->errorMessageEmpty = $errorMessageEmpty;

        return $this;
    }

    /**
     * Get the value of defaultValues
     */
    public function getDefaultValues(): array
    {
        return $this->defaultValues;
    }

    /**
     * Set the value of defaultValues
     *
     * @return self
     */
    public function setDefaultValues(array $defaultValues)
    {
        $this->defaultValues = $defaultValues;

        return $this;
    }

    public function hasDefaultValuesAny()
    {
        return ! $this->isUndefined($this->defaultValuesAny);
    }

    /**
     * Get the value of defaultValuesAny
     */
    public function getDefaultValueAny()
    {
        return $this->defaultValuesAny;
    }

    /**
     * Set the value of defaultValuesAny
     *
     * @return self
     */
    public function setDefaultValuesAny($defaultValuesAny)
    {
        $this->defaultValuesAny = $defaultValuesAny;

        return $this;
    }

    /**
     * Get the value of enableDefaultValueIfOptional
     */
    public function getEnableDefaultValueIfOptional(): bool
    {
        return $this->enableDefaultValueIfOptional;
    }

    /**
     * Set the value of enableDefaultValueIfOptional
     *
     * @return self
     */
    public function setEnableDefaultValueIfOptional(bool $enableDefaultValueIfOptional)
    {
        $this->enableDefaultValueIfOptional = $enableDefaultValueIfOptional;

        return $this;
    }

    /**
     * Get the value of defaultValueIfOptional
     */
    public function getDefaultValueIfOptional()
    {
        return $this->defaultValueIfOptional;
    }

    /**
     * Set the value of defaultValueIfOptional
     *
     * @return self
     */
    public function setDefaultValueIfOptional($defaultValueIfOptional)
    {
        $this->defaultValueIfOptional = $defaultValueIfOptional;

        return $this;
    }

    private function isUndefined($value)
    {
        return $value instanceof Undefined;
    }

    /**
     * Get the value of once
     */
    public function getOnce(): bool
    {
        return $this->once;
    }

    /**
     * Set the value of once
     *
     * @return self
     */
    public function setOnce(bool $once)
    {
        $this->once = $once;

        return $this;
    }

    public function hasCustomErrorMessage($ruleID)
    {
        return isset($this->getCustomErrMessages()[$ruleID]);
    }

    public function getCustomErrorMessage($ruleID)
    {
        if (isset($this->getCustomErrMessages()[$ruleID])) {
            return $this->getCustomErrMessages()[$ruleID];
        }

        return null;
    }

    public function hasRule($ruleID)
    {
        return isset($this->getMiddlewaresIds()[$ruleID]);
    }

    public function getRule(mixed $ruleID): ?Rule
    {
        $middlewares = $this->getMiddlewares();
        assert($middlewares instanceof LinkedList);

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

    /// from proxy

    public function getMiddlewares(): LinkedList
    {
        $linkedList = $this->middlewares;
        // $linkedList->rewind();

        return $linkedList;
    }

    public function getMiddlewaresAndRewind(): LinkedList
    {
        $linkedList = $this->middlewares;
        $linkedList->rewind();

        return $linkedList;
    }

    public function getRules(): ?array
    {
        return array_filter($this->getMiddlewares()->toArray(), fn ($e): bool => $e instanceof Rule);
    }

    // public function isDefaultValueEnabled(){
    // 	return $this->field->getSetup()->getEnableDefaultValueIfOptional();
    // }
    // public function getDefaultValue(){
    // 	return $this->field->getSetup()->getDefaultValueIfOptional();
    // }

    // public function checkRequired($fieldname, $body)
    // {
    // 	$rule = $this->getRule(RulesEnum::ID_REQUIRED->value);

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
        $rule = $this->getRule(RulesEnum::ID_NOT_NULL->value);

        if (! $rule instanceof \Kedniko\Vivy\Core\Rule) {
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
        $rule = $this->getRule(RulesEnum::ID_NOT_EMPTY_STRING->value);

        if (! $rule instanceof \Kedniko\Vivy\Core\Rule) {
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

    // public function hasTransformCallback()
    // {
    // 	return property_exists($this, 'transformCallback') && is_array($this->transformCallback) && count($this->transformCallback) > 0;
    // }
    // public function getTransformCallbackArray()
    // {
    // 	return $this->transformCallback;
    // }

    // public function setChildStateProperty(string $propertyName, mixed $value): void
    // {
    //     $parts = explode('.', $propertyName);
    //     $propertyName = array_shift($parts);
    //     $path = implode('.', $parts);
    //     $this->{$propertyName} = Arr::set($this->{$propertyName}, $path, $value);

    //     // $this->{$propertyName} = $value;
    //     // Arr::set($this->field->setup, $propertyName, $value);
    // }

    /**
     * Used in orProxy
     */
    public function getChildrenErrors()
    {
        return $this->_extra['errors'] ?? [];
    }
}
