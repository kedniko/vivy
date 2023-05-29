<?php

namespace Kedniko\Vivy\Core;

class State
{
    protected $data;

    protected $name;

    protected $types;

    /** @var bool */
    protected $required;

    /** @var bool */
    protected $notEmptyString;

    /** @var bool */
    protected $notNull;

    /** @var Rule */
    protected $requiredRule;

    /** @var Rule */
    protected $emptyStringRule;

    /** @var Rule */
    protected $notEmptyStringRule;

    /** @var Rule */
    protected $notNullRule;

    protected $defaultValues;

    protected $defaultValuesAny;

    protected $enableDefaultValueIfOptional;

    protected $defaultValueIfOptional;

    protected $customErrMessages;

    protected $errorMessageAny;

    protected $errorMessageEmpty;

    protected $valueIfOptionalNotExists;

    /** @var LinkedList */
    protected $middlewares;

    protected $middlewaresid;

    protected $onValid;

    protected $onError;

    protected $stopOnFailure;

    protected $once;

    public $setupFn;

    public $_extra;

    public $fields;

    public $requiredIf;

    public $requiredIfField;

    // /** @var array */
    // public $allow;
    // /** @var array */
    // public $deny;

    public function __construct()
    {
        $this->data = Undefined::instance();
        $this->name = Undefined::instance();
        $this->fields = new LinkedList();

        $this->customErrMessages = [];
        $this->defaultValues = [];
        $this->defaultValuesAny = Undefined::instance();
        $this->errorMessageAny = Undefined::instance();
        $this->errorMessageEmpty = Undefined::instance();
        $this->valueIfOptionalNotExists = Undefined::instance();
        $this->middlewares = new LinkedList();
        $this->middlewaresid = [];
        $this->onValid = [];
        $this->onError = [];

        $this->required = Undefined::instance();
        $this->requiredIf = Undefined::instance();
        $this->requiredIfField = Undefined::instance();
        $this->requiredRule = null;
        $this->notEmptyString = Undefined::instance();
        $this->notNull = Undefined::instance();

        $this->enableDefaultValueIfOptional = false;
        $this->defaultValueIfOptional = Undefined::instance();
        $this->stopOnFailure = false;
        $this->once = false;

        // $this->allow = [];
        // $this->deny = [];
    }

    public function hasData()
    {
        return ! $this->isUndefined($this->data);
    }

    /**
     * Get the value of data
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Set the value of data
     *
     * @return  self
     */
    public function setData($data)
    {
        $this->data = $data;

        return $this;
    }

    public function canBeEmptyString()
    {
        return $this->hasNotEmptyString() && $this->getNotEmptyString() === false;
    }

    public function hasNotEmptyString()
    {
        return ! $this->isUndefined($this->notEmptyString);
    }

    /**
     * Get the value of notEmptyString
     */
    public function getNotEmptyString()
    {
        return $this->notEmptyString;
    }

    /**
     * Set the value of notEmptyString
     *
     * @return  self
     */
    public function setNotEmptyString($notEmptyString, $rule = null)
    {
        $this->notEmptyString = $notEmptyString;
        if ($rule instanceof Rule) {
            $this->setRequiredRule($rule);
        }

        return $this;
    }

    public function isRequired()
    {
        return $this->getRequired() === true;
    }

    /**
     * Get the value of required
     */
    public function getRequired()
    {
        return $this->required;
    }

    public function issetRequired()
    {
        $isset = ! $this->isUndefined($this->required);

        return $isset;
    }

    /**
     * @param  bool  $required
     * @param  Rule  $rule
     */
    public function setRequired($required, $rule = null)
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

    public function getRequiredRule()
    {
        return $this->requiredRule;
    }

    public function getEmptyStringRule()
    {
        return $this->emptyStringRule;
    }

    public function getNotNullRule()
    {
        return $this->notNullRule;
    }

    public function setRequiredRule($rule)
    {
        $this->requiredRule = $rule;

        return $this;
    }

    public function setNotEmptyStringRule($rule)
    {
        $this->notEmptyStringRule = $rule;

        return $this;
    }

    public function setNotNullRule($rule)
    {
        $this->notNullRule = $rule;

        return $this;
    }

    public function removeRequiredRule()
    {
        $this->requiredRule = Undefined::instance();

        return $this;
    }

    public function canBeNull()
    {
        return $this->hasNotNull() && $this->getNotNull() === false;
    }

    public function hasNotNull()
    {
        return ! $this->isUndefined($this->notNull);
    }

    /**
     * Get the value of notNull
     */
    public function getNotNull()
    {
        return $this->notNull;
    }

    /**
     * Set the value of notNull
     *
     * @return  self
     */
    public function setNotNull($notNull)
    {
        $this->notNull = $notNull;

        return $this;
    }

    /**
     * Get the value of middlewares
     */
    /**
     * @return [type]
     */
    public function getMiddlewares()
    {
        return $this->middlewares;
    }

    public function hasMiddlewares()
    {
        return ! $this->middlewares->isEmpty();
    }

    /**
     * Set the value of middlewares
     *
     * @return  self
     */
    public function setMiddlewares($middlewares)
    {
        $this->middlewares = $middlewares;

        return $this;
    }

    /**
     * Get the value of middlewaresid
     */
    public function getMiddlewaresIds()
    {
        return $this->middlewaresid;
    }

    public function hasMiddlewareId($id)
    {
        return isset($this->middlewaresid[$id]);
    }

    /**
     * Set the value of middlewaresid
     *
     * @return  self
     */
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

    /**
     * Get the value of callbackIfOptionalNotExists
     */
    public function getValueIfOptionalNotExists()
    {
        return $this->valueIfOptionalNotExists;
    }

    /**
     * Set the value of callbackIfOptionalNotExists
     *
     * @param  callable  $value
     * @return  self
     */
    public function setValueIfOptionalNotExists($value)
    {
        $this->valueIfOptionalNotExists = $value;

        return $this;
    }

    public function hasName()
    {
        return ! $this->isUndefined($this->name);
    }

    /**
     * Get the value of name
     */
    public function getName(): string|Undefined
    {
        return $this->name;
    }

    /**
     * Set the value of name
     *
     * @return  self
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    public function hasStopOnFailure()
    {
        return ! $this->isUndefined($this->stopOnFailure);
    }

    /**
     * Get the value of stopOnFailure
     */
    public function getStopOnFailure()
    {
        return $this->stopOnFailure;
    }

    /**
     * Set the value of stopOnFailure
     *
     * @return  self
     */
    public function setStopOnFailure($stopOnFailure)
    {
        $this->stopOnFailure = $stopOnFailure;

        return $this;
    }

    /**
     * Get the value of customErrMessages
     */
    public function getCustomErrMessages()
    {
        return $this->customErrMessages;
    }

    /**
     * Set the value of customErrMessages
     *
     * @return  self
     */
    public function setCustomErrMessages($customErrMessages)
    {
        $this->customErrMessages = $customErrMessages;

        return $this;
    }

    /**
     * Get the value of fields
     */
    public function getFields()
    {
        return $this->fields;
    }

    /**
     * Set the value of fields
     *
     * @return  self
     */
    public function setFields($types)
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
    public function getErrorMessageAny()
    {
        return $this->errorMessageAny;
    }

    /**
     * Set the value of errorMessageAny
     *
     * @return  self
     */
    public function setErrorMessageAny($errorMessageAny)
    {
        $this->errorMessageAny = $errorMessageAny;

        return $this;
    }

    /**
     * Get the value of onValid
     */
    public function getOnValid()
    {
        return $this->onValid;
    }

    /**
     * Set the value of onValid
     *
     * @return  self
     */
    public function setOnValid($onValid)
    {
        $this->onValid = $onValid;

        return $this;
    }

    public function addOnValid($onValid)
    {
        $this->onValid[] = $onValid;

        return $this;
    }

    /**
     * Get the value of onError
     */
    public function getOnError()
    {
        return $this->onError;
    }

    /**
     * Set the value of onError
     *
     * @return  self
     */
    public function setOnError($onError)
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
    public function getErrorMessageEmpty()
    {
        return $this->errorMessageEmpty;
    }

    /**
     * Set the value of errorMessageEmpty
     *
     * @return  self
     */
    public function setErrorMessageEmpty($errorMessageEmpty)
    {
        $this->errorMessageEmpty = $errorMessageEmpty;

        return $this;
    }

    /**
     * Get the value of defaultValues
     */
    public function getDefaultValues()
    {
        return $this->defaultValues;
    }

    /**
     * Set the value of defaultValues
     *
     * @return  self
     */
    public function setDefaultValues($defaultValues)
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
     * @return  self
     */
    public function setDefaultValuesAny($defaultValuesAny)
    {
        $this->defaultValuesAny = $defaultValuesAny;

        return $this;
    }

    /**
     * Get the value of enableDefaultValueIfOptional
     */
    public function getEnableDefaultValueIfOptional()
    {
        return $this->enableDefaultValueIfOptional;
    }

    /**
     * Set the value of enableDefaultValueIfOptional
     *
     * @return  self
     */
    public function setEnableDefaultValueIfOptional($enableDefaultValueIfOptional)
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
     * @return  self
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
    public function getOnce()
    {
        return $this->once;
    }

    /**
     * Set the value of once
     *
     * @param  bool  $once
     * @return  self
     */
    public function setOnce($once)
    {
        $this->once = $once;

        return $this;
    }
}
