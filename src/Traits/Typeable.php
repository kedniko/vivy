<?php

declare(strict_types=1);

namespace Kedniko\Vivy\Traits;

use Closure;
use Kedniko\Vivy\ArrayContext;
use Kedniko\Vivy\Callback;
use Kedniko\Vivy\Context;
use Kedniko\Vivy\Contracts\ContextInterface;
use Kedniko\Vivy\Contracts\MiddlewareInterface;
use Kedniko\Vivy\Contracts\TypeInterface;
use Kedniko\Vivy\Core\Args;
use Kedniko\Vivy\Core\GroupContext;
use Kedniko\Vivy\Core\hasMagicCall;
use Kedniko\Vivy\Core\Helpers;
use Kedniko\Vivy\Core\LinkedList;
use Kedniko\Vivy\Core\Node;
use Kedniko\Vivy\Core\Options;
use Kedniko\Vivy\Core\Rule;
use Kedniko\Vivy\Core\Setup;
use Kedniko\Vivy\Core\Undefined;
use Kedniko\Vivy\Core\Validated;
use Kedniko\Vivy\Core\Validator;
use Kedniko\Vivy\Enum\RulesEnum as CoreRulesEnum;
use Kedniko\Vivy\Exceptions\VivyException;
use Kedniko\Vivy\Messages\RuleMessage;
use Kedniko\Vivy\Rules as CoreRules;
use Kedniko\Vivy\Serializer;
use Kedniko\Vivy\Support\Invader;
use Kedniko\Vivy\Support\Util;
use Kedniko\Vivy\Transformer;
use Kedniko\Vivy\Type;
use Kedniko\Vivy\V;
use Kedniko\VivyPluginStandard\Enum\RulesEnum;

trait Typeable
{
    use hasMagicCall;

    public Setup $setup;

    public bool $skipOtherMiddlewares = false;

    public bool $skipOtherRules = false;

    public mixed $value;

    public array $errors = [];

    public bool $canBeEmptyString;

    public bool $canBeNull;

    public TypeInterface $field;

    public array $_extra;

    public function __construct()
    {
        $this->setup = new Setup();
    }

    public function getSetup(): Setup
    {
        return $this->setup;
    }

    public function setSetup(Setup $setup): void
    {
        $this->setup = $setup;
    }

    public function required(?Options $options = null)
    {

        $options = Helpers::getOptions($options);
        $errormessage = $options->getErrorMessage() ?: RuleMessage::getErrorMessage(CoreRulesEnum::ID_REQUIRED->value);
        if ($this->getSetup()->hasRule(CoreRulesEnum::ID_REQUIRED->value)) {
            $this->removeRule(CoreRulesEnum::ID_REQUIRED->value);
        }
        $rule = $this->prepareRule(CoreRules::required($errormessage), $options);
        $this->getSetup()->setRequired(true, $rule);

        return $this;
    }

    public function notNull(?Options $options = null)
    {

        $options = Helpers::getOptions($options);
        $errormessage = $options->getErrorMessage() ?: RuleMessage::getErrorMessage(CoreRulesEnum::ID_NOT_NULL->value);
        $this->addRule(CoreRules::notNull($errormessage), $options);
        $this->getSetup()->setNotNull(true);

        return $this;
    }

    // public function once(): void
    // {
    //   $this->getSetup()->setOnce(true);
    // }

    /**
     * @param  Rule|Transformer|callable  $middleware
     */
    private function addMiddleware($middleware, ?Options $options = null): void
    {

        $linkedlist = $this->getSetup()->getMiddlewares();
        assert($linkedlist instanceof LinkedList);

        if ($options && $options->getAppendAfterCurrent()) {
            $linkedlist->appendAfterCurrent(new Node($middleware));
        } else {
            $linkedlist->append(new Node($middleware));
        }
        $this->getSetup()->addMiddlewareId($middleware->getID());
    }

    protected function prependMiddleware(Rule|Transformer|callable $middleware)
    {

        $linkedlist = $this->getSetup()->getMiddlewares();
        assert($linkedlist instanceof LinkedList);
        $linkedlist->prepend(new Node($middleware));
        $this->getSetup()->addMiddlewareId($middleware->getID());
    }

    private function removeMiddleware(string $middlewareid, bool $removeOnlyOne = false, bool $hardRemove = true): void
    {
        $type = $this;

        if ($hardRemove) {
            $linkedlist = $type->getSetup()->getMiddlewares();
            assert($linkedlist instanceof LinkedList);

            $linkedlist->remove(fn (MiddlewareInterface $middleware): bool => $middleware->getID() === $middlewareid, $removeOnlyOne);
        }

        $ids = $type->getSetup()->getMiddlewaresIds();
        assert($type instanceof TypeInterface);
        unset($ids[$middlewareid]);
    }

    protected function prepareRule(Rule $rule, ?Options $options = null)
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

    public function addRule(Rule $rule, ?Options $options = null)
    {
        $rule = $this->prepareRule($rule, $options);

        $this->addMiddleware($rule, $options);

        return $this;
    }

    public function stopOnFailure()
    {

        $this->getSetup()->setStopOnFailure(true);

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

    private function getTransfomerFromString(string $globalFnName)
    {
        return V::transformer(
            $globalFnName,
            function (ContextInterface $c) use ($globalFnName) {
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

                return call_user_func_array($globalFnName, $args);
            }
        );
    }

    /**
     * @param  null  $args  (optional) `...$args` arguments to pass to the function
     */
    public function addTransformer(Transformer|callable|string $transformer, ?Options $options = null)
    {

        $transformer = $this->prepareTransformer($transformer, $options);
        $this->addMiddleware($transformer);

        return $this;
    }

    public function prepareTransformer(Transformer|callable|string $transformer, ?Options $options = null)
    {
        $options = Helpers::getOptions($options);

        if (
            ! $transformer instanceof Transformer &&
            ! is_string($transformer) &&
            ! is_callable($transformer)
        ) {
            throw new VivyException('Expected type Transformer or function name', 1);
        }

        if (is_callable($transformer)) {
            if (is_string($transformer)) {
                $transformer = $this->getTransfomerFromString($transformer);
            } else {
                $transformer = V::transformer('transformer', $transformer);
            }
        }

        $errormessage = $options->getErrorMessage();
        $stopOnFailure = $options->getStopOnFailure();

        if ($stopOnFailure !== null) {
            $transformer->setStopOnFailure($stopOnFailure);
        }

        if ($errormessage) {
            $transformer->setErrorMessage($errormessage);
        }

        $transformer->setOptions($options);

        return $transformer;
    }

    public function tap(callable $callback)
    {
        $this->addCallback(new Callback('tap', $callback));

        return $this;
    }

    public function addCallback(Callback $callback, ?Options $options = null)
    {

        $callback = $this->prepareCallBack($callback, $options);
        $this->addMiddleware($callback);

        return $this;
    }

    public function prepareCallBack(Callback $callback, ?Options $options = null)
    {
        $options = Helpers::getOptions($options);

        if (is_callable($callback)) {
            $callback = V::callback('_call', $callback, $options);
        }

        $callback->setOptions($options);

        return $callback;
    }

    /**
     * HARD REMOVE: Remove from list and cache
     * SOFT REMOVE: remove only from cache (better performance)
     */
    public function removeTransformer(string $transformerID, bool $hardRemove = true)
    {
        // $this->getSetup()->getmiddlewares() = array_filter($this->getSetup()->getmiddlewares(), function ($e) use ($transformerID) {
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

        if (! is_array($rulesID)) {
            $rulesID = [$rulesID];
        }
        foreach ($rulesID as $ruleID) {
            $this->getSetup()->getCustomErrMessages()[$ruleID] = $errormessage;
        }

        return $this;
    }

    /**
     * Set an error message for rules: `required`, `notEmptyString`, `notNull`
     */
    public function setErrorMessageEmpty(string $errormessage)
    {

        // $rulesID = [RulesEnum::ID_REQUIRED->value, RulesEnum::ID_NOT_EMPTY_STRING->value, RulesEnum::ID_NOT_NULL->value];
        // foreach ($rulesID as $ruleID) {
        // 	$this->getSetup()->getcustomErrMessages()[$ruleID] = $errormessage;
        // }
        $this->getSetup()->setErrorMessageEmpty($errormessage);

        return $this;
    }

    public function setErrorMessageAny(string $errormessage)
    {

        $this->getSetup()->setErrorMessageAny($errormessage);

        return $this;
    }

    /**
     * * Example:
     * [RulesEnum::ID_REQUIRED->value=>'Error: required', RulesEnum::ID_NOT_NULL->value=>'Error: notNull']
     */
    public function setErrorMessageFromArray(array $errormessages)
    {

        $this->getSetup()->setCustomErrMessages($errormessages);

        return $this;
    }

    /**
     * fn(Context $c)
     */
    public function onValid(callable $callback)
    {

        $this->getSetup()->addOnValid($callback);

        return $this;
    }

    public function onError(callable $callback, string|array|null $rulesID = null)
    {

        if ($rulesID) {
            if (! is_array($rulesID)) {
                $rulesID = [$rulesID];
            }
            foreach ($rulesID as $ruleID) {
                $this->getSetup()->addOnError($callback, $ruleID);
            }
        } else {
            $this->getSetup()->addOnError($callback);
        }

        return $this;
    }

    public function setDefaultOnError($rulesID, $value)
    {

        if (! is_array($rulesID)) {
            $rulesID = [$rulesID];
        }
        foreach ($rulesID as $ruleID) {
            $this->getSetup()->getDefaultValues()[$ruleID] = $value;
        }

        return $this;
    }

    /**
     * Catch all errors and set a default value
     */
    public function catchAll(mixed $value)
    {
        $this->getSetup()->setDefaultValuesAny($value);

        return $this;
    }

    public function getStopOnFailure()
    {
        if (! $this->getSetup()->hasStopOnFailure()) {
            return false;
        }

        return $this->getSetup()->getStopOnFailure() === true;
    }

    private function setName(string|Undefined $name): void
    {
        $this->getSetup()->setName($name);
    }

    public function getName(): string|Undefined
    {
        return $this->getSetup()->getName();
    }

    private function getArrayContext($index, mixed $failsCount): ArrayContext
    {
        $c = new ArrayContext();
        $c->setIndex($index);
        $c->setExtra('failsCount', $failsCount);

        return $c;
    }

    public function getContext(?ContextInterface $fatherContext = null): ContextInterface|ArrayContext|GroupContext
    {
        // child context is the same as father context
        $isInsideOr = isset($this->_extra['isInsideOr']) && $this->_extra['isInsideOr'] === true;
        if ($isInsideOr) {
            return $fatherContext;
        }

        $isArrayContext = isset($this->_extra['isArrayContext']) && $this->_extra['isArrayContext'] === true;
        $isGroupContext = Helpers::isNotUndefined($this->getSetup()->getName());

        if ($isArrayContext) {
            $c = $this->getArrayContext($this->_extra['index'], $this->_extra['failsCount']);
        } else {
            $isGroupContext = Helpers::isNotUndefined($this->getSetup()->getName());

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

    public static function shareState(TypeInterface $from, TypeInterface $to)
    {
        if ($from instanceof TypeInterface) {
            $to->setup = $from->setup; // share setup
        }

        return $to;
    }

    public static function new(Typeable|TypeInterface|null $from = null)
    {
        $thisObj = new static();
        if ($from instanceof TypeInterface) {
            self::shareState($from, $thisObj);
        }

        return $thisObj;
    }

    public function from(?TypeInterface $obj)
    {
        if ($obj instanceof TypeInterface) {
            $this->setup = $obj->setup; // share setup
        }

        return $this;
    }

    // Validation methods

    public function validate(mixed $value = null, ?ContextInterface $fatherContext = null): Validated
    {
        $validator = new Validator($this, $fatherContext);

        return $validator->validate($value);
    }

    /**
     * @param  mixed  $value  Overrides the old one if exists
     */
    public function errors(mixed $value = null): array
    {
        $validator = new Validator($this);

        return $validator->errors($value);
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
     * @param  mixed  $value  Overrides the old one if exists
     */
    public function failsWith(mixed $value): bool
    {
        return ! $this->isValidWith($value);
    }

    /**
     * @param  Closure|mixed  $callback_or_value
     */
    public function setValue($callback_or_value, ?Options $options = null)
    {
        $options = Options::build($options, Util::getRuleArgs(__METHOD__, func_get_args()), __METHOD__);

        $callback = ($callback_or_value instanceof Closure) ? $callback_or_value : fn () => $callback_or_value;
        $transformer = new Transformer(RulesEnum::ID_SET_VALUE->value, $callback);
        $type = Type::new(from: $this);
        $type->addTransformer($transformer, $options);

        return $type;
    }

    /**
     * @param  bool|null  $stopOnFailure
     * @param  array|null  $args
     */
    public function prependRule(Rule $rule, ?Options $options = null)
    {
        // return parent::prependRule($rule, $options);

        $rule = $this->prepareRule($rule, $options);
        (new Invader($this->type))->prependMiddleware($rule);

        return $this;
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

    // 	$options = Helpers::getOptions($options);
    // 	$errormessage = $options->getErrormessage() ?: RuleMessage::getErrorMessage(RulesEnum::ID_NOT_EMPTY_STRING->value);
    // 	$this->addRule(Rules::notEmptyString($errormessage), $options);
    // 	$this->getSetup()->setNotEmptyString(true);
    // 	return $this;
    // }

    // public function allow($value, $stopOnMatch = true)
    // {
    // 	$this->getSetup()->allow[] = [
    // 		'value'       => $value,
    // 		'stopOnMatch' => $stopOnMatch,
    // 	];
    // 	return $this;
    // }
    // public function deny($value)
    // {
    // 	$this->getSetup()->deny[] = $value;
    // 	return $this;
    // }
    // public function allowEmptyString()
    // {
    // 	$this->removeRule(RulesEnum::ID_NOT_EMPTY_STRING->value);
    // 	$this->getSetup()->setNotEmptyString(false);
    // 	return $this;
    // }
    // public function allowNull()
    // {
    // 	$this->removeRule(RulesEnum::ID_NOT_NULL->value);
    // 	$this->getSetup()->setNotNull(false);
    // 	return $this;
    // }

    // public function setErrorMessageRequired($errormessage)
    // {
    // 	$this->getSetup()->getcustomErrMessages()[RulesEnum::ID_REQUIRED->value] = $errormessage;
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
    //  * @param TypeInterface $sourceType
    //  */
    // public function use(Type $sourceType)
    // {
    // 	$this->setup = $sourceType->setup;
    // 	return $this;
    // }

    // protected function prependRule(Rule $rule, Options $options = null)
    // {
    // 	$rule = $this->prepareRule($rule, $options);
    // 	$this->prependMiddleware($rule);
    // 	return $this;
    // }

    public function allowNull()
    {
        $this->removeRule(CoreRulesEnum::ID_NOT_NULL->value);
        $this->getSetup()->setNotNull(false);

        return $this;
    }

    public function serialize(): array
    {
        assert($this instanceof TypeInterface);

        return (new Serializer)->encode($this);
    }

    public function clone()
    {
        throw new \Exception('Not implemented');
    }
}
