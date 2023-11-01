<?php

namespace Kedniko\Vivy;

use Kedniko\Vivy\Contracts\ContextInterface;
use Kedniko\Vivy\Contracts\MiddlewareInterface;
use Kedniko\Vivy\Core\Args;
use Kedniko\Vivy\Core\GroupContext;
use Kedniko\Vivy\Core\hasMagicCall;
use Kedniko\Vivy\Core\Helpers;
use Kedniko\Vivy\Core\LinkedList;
use Kedniko\Vivy\Core\Node;
use Kedniko\Vivy\Core\Options;
use Kedniko\Vivy\Core\Rule;
use Kedniko\Vivy\Core\State;
use Kedniko\Vivy\Core\Undefined;
use Kedniko\Vivy\Core\Validated;
use Kedniko\Vivy\Core\Validator;
use Kedniko\Vivy\Exceptions\VivyException;
use Kedniko\Vivy\Messages\RuleMessage;
use Kedniko\Vivy\Plugins\StandardLibrary\Rules;
use Kedniko\Vivy\Support\TypeProxy;

class Type
{
    use hasMagicCall;

    public State $state;

    public ContextInterface $context;

    public TypeProxy $typeProxy;

    public bool $skipOtherMiddlewares = false;

    public bool $skipOtherRules = false;

    public mixed $value;

    public array $errors = [];

    public bool $canBeEmptyString;

    public bool $canBeNull;

    public Type $field;

    public TypeProxy $fieldProxy;

    public array $_extra;

    public function __construct()
    {
        $this->state = new State();
    }

    public function required(Options $options = null)
    {
        $_this = $this->getThisUnwrapped();

        $options = Helpers::getOptions($options);
        $errormessage = $options->getErrorMessage() ?: RuleMessage::getErrorMessage(Rules::ID_REQUIRED);
        if ((new TypeProxy($this))->hasRule(Rules::ID_REQUIRED)) {
            (new TypeProxy($this))->removeRule(Rules::ID_REQUIRED);
        }
        $rule = $this->prepareRule(Rules::required($errormessage), $options);
        $_this->state->setRequired(true, $rule);

        return $this;
    }

    public function notNull(Options $options = null)
    {
        $_this = $this->getThisUnwrapped();

        $options = Helpers::getOptions($options);
        $errormessage = $options->getErrorMessage() ?: RuleMessage::getErrorMessage(Rules::ID_NOT_NULL);
        $this->addRule(Rules::notNull($errormessage), $options);
        $_this->state->setNotNull(true);

        return $this;
    }

    public function once(): void
    {
        $this->state->setOnce(true);
    }

    /**
     * @param  Rule|Transformer|callable  $middleware
     */
    private function addMiddleware($middleware, Options $options = null): void
    {
        $_this = $this->getThisUnwrapped();

        /** @var LinkedList $linkedlist */
        $linkedlist = $_this->state->getMiddlewares();
        if ($options && $options->getAppendAfterCurrent()) {
            $linkedlist->appendAfterCurrent(new Node($middleware));
        } else {
            $linkedlist->append(new Node($middleware));
        }
        $_this->state->addMiddlewareId($middleware->getID());
    }

    private function getThisUnwrapped()
    {
        return $this instanceof TypeProxy ? $this->field : $this;
    }

    protected function prependMiddleware(Rule|Transformer|callable $middleware)
    {
        $_this = $this->getThisUnwrapped();

        /** @var LinkedList $linkedlist */
        $linkedlist = $_this->state->getMiddlewares();
        $linkedlist->prepend(new Node($middleware));
        $_this->state->addMiddlewareId($middleware->getID());
    }

    private function removeMiddleware(string $middlewareid, bool $removeOnlyOne = false, bool $hardRemove = true): void
    {
        $type = $this;

        if ($type instanceof TypeProxy) {
            $type = $this->field;
        }

        if ($hardRemove) {
            /** @var LinkedList $linkedlist */
            $linkedlist = $type->state->getMiddlewares();
            $linkedlist->remove(fn (MiddlewareInterface $middleware): bool => $middleware->getID() === $middlewareid, $removeOnlyOne);
        }

        /** @var Type $type */
        $ids = $type->state->getMiddlewaresIds();
        unset($ids[$middlewareid]);
    }

    protected function prepareRule(Rule $rule, Options $options = null)
    {
        $options = Helpers::getOptions($options);

        $options = clone $options;

        if ($options->getStopOnFailure() === null) {
            $options->stopOnFailure();
        }
        $options->message($options->getErrorMessage() ?: $rule->getErrorMessage());

        $rule->setOptions($options);

        return $rule;
    }

    public function addRule(Rule $rule, Options $options = null)
    {
        $rule = $this->prepareRule($rule, $options);

        $this->addMiddleware($rule, $options);

        return $this;
    }

    public function stopOnFailure()
    {
        $_this = $this->getThisUnwrapped();

        $_this->state->setStopOnFailure(true);

        return $this;
    }

    /**
     * HARD REMOVE: Remove from list and cache
     * SOFT REMOVE: remove only from cache (better performance)
     */
    public function removeRule(string $ruleID, bool $hardRemove = true)
    {
        $this->removeMiddleware($ruleID, false, $hardRemove);

        return $this;
    }

    /**
     * @param  null  $args (optional) `...$args` arguments to pass to the function
     */
    public function addTransformer(Transformer|callable|string $transformer, Options $options = null)
    {
        $options = Helpers::getOptions($options);

        if (
            ! $transformer instanceof Transformer &&
            ! is_string($transformer) &&
            ! is_callable($transformer)
        ) {
            throw new VivyException('Expected type Transformer or function name', 1);
        }

        $args = array_slice(func_get_args(), 2);

        if (is_callable($transformer)) {
            if (is_string($transformer)) {
                $transformer = V::transformer(
                    $transformer,
                    function (ContextInterface $c) use ($transformer) {
                        $args = [];

                        if ($c->args()) {
                            foreach ($c->args() as $arg) {
                                if (is_callable($arg)) {
                                    $arg = $arg($c);
                                    if (is_array($arg)) {
                                        $args = array_merge($args, $arg);
                                    } elseif ($arg instanceof Args) {
                                        $args = array_merge($args, $arg->args);
                                    } else {
                                        $args[] = $arg;
                                    }
                                } elseif ($arg instanceof Args) {
                                    $args = array_merge($args, $arg->args);
                                } else {
                                    $args[] = $arg;
                                }
                            }
                        } else {
                            $args = [$c->value];
                        }

                        return call_user_func_array($transformer, $args);
                    }
                );
            } else {
                $transformer = V::transformer('transformer', $transformer);
            }
        }

        $errormessage = $options->getErrorMessage();
        $stopOnFailure = $options->getStopOnFailure();

        if ($stopOnFailure !== null) {
            $transformer->setStopOnFailure($stopOnFailure);
        }
        $transformer->setArgs($args);

        if ($errormessage) {
            $transformer->setErrorMessage($errormessage);
        }

        $this->addMiddleware($transformer);

        return $this;
    }

    public function tap(callable $callback)
    {
        $this->addCallback(new Callback('tap', $callback));

        return $this;
    }

    public function addCallback(Callback $callback, Options $options = null)
    {
        $options = Helpers::getOptions($options);

        if (is_callable($callback)) {
            $callback = V::callback(null, $callback, $options);
        }
        $this->addMiddleware($callback);

        return $this;
    }

    /**
     * HARD REMOVE: Remove from list and cache
     * SOFT REMOVE: remove only from cache (better performance)
     */
    public function removeTransformer(string $transformerID, bool $hardRemove = true)
    {
        // $_this->state->getmiddlewares() = array_filter($_this->state->getmiddlewares(), function ($e) use ($transformerID) {
        // 	if ($e instanceof Transformer && $e->getID() === $transformerID) {
        // 		return false;
        // 	}
        // 	return true;
        // });
        $this->removeMiddleware($transformerID, $hardRemove);

        return $this;
    }

    public function setErrorMessage(string|array $rulesID, mixed $errormessage)
    {
        $_this = $this->getThisUnwrapped();

        if (! is_array($rulesID)) {
            $rulesID = [$rulesID];
        }
        foreach ($rulesID as $ruleID) {
            $_this->state->getCustomErrMessages()[$ruleID] = $errormessage;
        }

        return $this;
    }

    /**
     * Set an error message for rules: `required`, `notEmptyString`, `notNull`
     */
    public function setErrorMessageEmpty(string $errormessage)
    {
        $_this = $this->getThisUnwrapped();

        // $rulesID = [Rules::ID_REQUIRED, Rules::ID_NOT_EMPTY_STRING, Rules::ID_NOT_NULL];
        // foreach ($rulesID as $ruleID) {
        // 	$_this->state->getcustomErrMessages()[$ruleID] = $errormessage;
        // }
        $_this->state->setErrorMessageEmpty($errormessage);

        return $this;
    }

    public function setErrorMessageAny(string $errormessage)
    {
        $_this = $this->getThisUnwrapped();

        $_this->state->setErrorMessageAny($errormessage);

        return $this;
    }

    /**
     * * Example:
     * [Rules::ID_REQUIRED=>'Error: required', Rules::ID_NOT_NULL=>'Error: notNull']
     */
    public function setErrorMessageFromArray(string $errormessages)
    {
        $_this = $this->getThisUnwrapped();

        $_this->state->setCustomErrMessages($errormessages);

        return $this;
    }

    /**
     * fn(Context $c)
     */
    public function onValid(callable $callback)
    {
        $_this = $this->getThisUnwrapped();

        $_this->state->addOnValid($callback);

        return $this;
    }

    public function onError(callable $callback, string|array $rulesID = null)
    {
        $_this = $this->getThisUnwrapped();

        if ($rulesID) {
            if (! is_array($rulesID)) {
                $rulesID = [$rulesID];
            }
            foreach ($rulesID as $ruleID) {
                $_this->state->addOnError($callback, $ruleID);
            }
        } else {
            $_this->state->addOnError($callback);
        }

        return $this;
    }

    public function setDefaultOnError($rulesID, $value)
    {
        $_this = $this->getThisUnwrapped();

        if (! is_array($rulesID)) {
            $rulesID = [$rulesID];
        }
        foreach ($rulesID as $ruleID) {
            $_this->state->getDefaultValues()[$ruleID] = $value;
        }

        return $this;
    }

    /**
     * Catch all errors and set a default value
     */
    public function catchAll(mixed $value)
    {
        $this->state->setDefaultValuesAny($value);

        return $this;
    }

    public function getStopOnFailure()
    {
        if (! $this->state->hasStopOnFailure()) {
            return false;
        }

        return $this->state->getStopOnFailure() === true;
    }

    private function setName(string|Undefined $name): void
    {
        $this->state->setName($name);
    }

    public function getName(): string|Undefined
    {
        return $this->state->getName();
    }

    private function getArrayContext($index, mixed $failsCount): ArrayContext
    {
        $c = new ArrayContext();
        $c->setIndex($index);
        $c->setExtra('failsCount', $failsCount);

        return $c;
    }

    public function getContext(ContextInterface $fatherContext = null): ContextInterface|ArrayContext|GroupContext
    {
        // child context is the same as father context
        $isInsideOr = isset($this->_extra['isInsideOr']) && $this->_extra['isInsideOr'] === true;
        if ($isInsideOr) {
            return $fatherContext;
        }

        $isArrayContext = isset($this->_extra['isArrayContext']) && $this->_extra['isArrayContext'] === true;
        $isGroupContext = Helpers::isNotUndefined($this->fieldProxy->getName());

        if ($isArrayContext) {
            $c = $this->getArrayContext($this->_extra['index'], $this->_extra['failsCount']);
        } else {
            $isGroupContext = Helpers::isNotUndefined($this->fieldProxy->getName());

            if ($isGroupContext) {
                $fieldname = $this->getName();
                $c = new GroupContext($fieldname);
            } else {
                $c = new Context();
            }
        }

        $c->setField($this);

        $c->setFatherContext($fatherContext);

        if ($c->rootContext() === null) {
            if ($fatherContext instanceof Context) {
                $c->setRootContext($fatherContext->rootContext());
            } else {
                $c->setRootContext($c);
                $c->setIsRootContext(true);
            }
        }

        return $c;
    }

    public function from(?Type $obj)
    {
        if ($obj instanceof \Kedniko\Vivy\Type) {
            $this->state = $obj->state; // share state
        }

        return $this;
    }

    // Validation methods

    public function validate(mixed $value = null, ContextInterface $fatherContext = null): Validated
    {
        return (new Validator($this))->validate($value, $fatherContext);
    }

    /**
     * @param  mixed  $value Overrides the old one if exists
     */
    public function errors(mixed $value = null): array
    {
        return (new Validator($this))->errors($value);
    }

    public function isValid(): bool
    {
        return (new Validator($this))->isValid();
    }

    public function fails(): bool
    {
        return ! $this->isValid();
    }

    public function isValidWith(mixed $value): bool
    {
        return (new Validator($this))->isValidWith($value);
    }

    /**
     * @param  mixed  $value Overrides the old one if exists
     */
    public function failsWith(mixed $value): bool
    {
        return ! $this->isValidWith($value);
    }

    /**
     * @param  callable|mixed  $callback_or_value
     */
    public function setValue(mixed $callback_or_value)
    {
        return (new Validator($this))->setValue($callback_or_value);
    }

    // to decide

    // public function removeOnValid() {
    // 	$this->onValid = null;
    // }
    // public function removeOnError() {
    // 	$this->onError = null;
    // 	return $this;
    // }

    /**
     * @param  Rule[]  $rules
     * @param  bool  $stopOnFirstSuccess
     * @param  Options|null  $options
     */
    // public function orOperator($rules, $stopOnFirstSuccess = true, Options $options = null)
    // {
    // 	$options = Helpers::getOptions($options);
    // 	// $this->addRule(Rules::atLeastOne($rules, $stopOnFirstSuccess, $options->getErrormessage()), $options);
    // 	return $this;
    // }

    // public function notEmptyString(Options $options = null)
    // {
    // 	$_this = $this->get_this();

    // 	$options = Helpers::getOptions($options);
    // 	$errormessage = $options->getErrormessage() ?: RuleMessage::getErrorMessage(Rules::ID_NOT_EMPTY_STRING);
    // 	$this->addRule(Rules::notEmptyString($errormessage), $options);
    // 	$_this->state->setNotEmptyString(true);
    // 	return $this;
    // }

    // public function allow($value, $stopOnMatch = true)
    // {
    // 	$this->state->allow[] = [
    // 		'value'       => $value,
    // 		'stopOnMatch' => $stopOnMatch,
    // 	];
    // 	return $this;
    // }
    // public function deny($value)
    // {
    // 	$this->state->deny[] = $value;
    // 	return $this;
    // }
    // public function allowEmptyString()
    // {
    // 	$_this = $this->get_this();
    // 	$this->removeRule(Rules::ID_NOT_EMPTY_STRING);
    // 	$_this->state->setNotEmptyString(false);
    // 	return $this;
    // }
    // public function allowNull()
    // {
    // 	$_this = $this->get_this();
    // 	$this->removeRule(Rules::ID_NOT_NULL);
    // 	$_this->state->setNotNull(false);
    // 	return $this;
    // }

    // public function setErrorMessageRequired($errormessage)
    // {
    // 	$_this->state->getcustomErrMessages()[Rules::ID_REQUIRED] = $errormessage;
    // 	return $this;
    // }

    // /**
    //  * @param Transformer|callable $transformer
    //  *
    //  */
    // public function addTransformer($transformer, $stopOnFailure = true, $args = null)
    // {
    // 	// if (
    // 	// 	!$transformer instanceof Transformer &&
    // 	// 	!is_string($transformer) &&
    // 	// 	!is_callable($transformer)
    // 	// ) {
    // 	// }

    // 	if (is_string($transformer) && is_callable($transformer)) {
    // 		$transformer = Vivy::transformer($transformer, function (Context $c) use ($transformer) {
    // 			return $transformer($c->value ?: '');
    // 		});
    // 	}

    // 	if (!$transformer instanceof Transformer) {
    // 		throw new VivyException("Expected type Transformer|callable", 1);
    // 	}

    // 	$this->addMiddleware($transformer);

    // 	if ($stopOnFailure !== null) {
    // 		$transformer->setStopOnFailure($stopOnFailure);
    // 	}
    // 	if ($args !== null) {
    // 		$transformer->setArgs($args);
    // 	}
    // 	return $this;
    // }

    // /**
    //  * Substitute a field with another
    //  *
    //  */
    // public function switchMap($type){
    //
    // }

    // public function tap(callable $callback){
    // 	$this->addMiddleware($callback);
    // }

    // /**
    //  * @param Type $sourceType
    //  */
    // public function use(Type $sourceType)
    // {
    // 	$this->state = $sourceType->state;
    // 	return $this;
    // }

    // protected function prependRule(Rule $rule, Options $options = null)
    // {
    // 	$rule = $this->prepareRule($rule, $options);
    // 	$this->prependMiddleware($rule);
    // 	return $this;
    // }
}
