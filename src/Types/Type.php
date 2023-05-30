<?php

namespace Kedniko\Vivy\Types;

use Kedniko\Vivy\ArrayContext;
use Kedniko\Vivy\Call\TraitUserDefinedCallBasic;
use Kedniko\Vivy\Callback;
use Kedniko\Vivy\Context;
use Kedniko\Vivy\Core\Args;
use Kedniko\Vivy\Core\ContextProxy;
use Kedniko\Vivy\Core\GroupContext;
use Kedniko\Vivy\Core\Helpers;
use Kedniko\Vivy\Core\LinkedList;
use Kedniko\Vivy\Core\Middleware;
use Kedniko\Vivy\Core\Node;
use Kedniko\Vivy\Core\Options;
use Kedniko\Vivy\Core\Rule;
use Kedniko\Vivy\Core\State;
use Kedniko\Vivy\Core\Undefined;
use Kedniko\Vivy\Core\Validated;
use Kedniko\Vivy\Exceptions\VivyException;
use Kedniko\Vivy\Messages\RuleMessage;
use Kedniko\Vivy\Messages\TransformerMessage;
use Kedniko\Vivy\Plugins\StandardLibrary\TypeOr;
use Kedniko\Vivy\Rules;
use Kedniko\Vivy\Transformer;
use Kedniko\Vivy\TypesProxy\TypeProxy;
use Kedniko\Vivy\V;

class Type
{
    use TraitUserDefinedCallBasic;

    /** @var State */
    public $state = null;

    /** @var Context */
    protected $context = null;

    /** @var Validated */
    protected $validated = null;

    /** @var TypeProxy */
    protected $typeProxy = null;

    /** @var bool */
    protected $skipOtherMiddlewares = false;

    /** @var bool */
    protected $skipOtherRules = false;

    protected $value = null;

    protected $errors = [];

    protected bool $canBeEmptyString;

    protected bool $canBeNull;

    protected $field;

    /**
     * @var TypeProxy
     */
    protected $fieldProxy = null;

    public $_extra;

    public function __construct()
    {
        $this->state = new State();
    }

    public function required(Options $options = null)
    {
        $_this = $this->get_this();

        $options = Helpers::getOptions($options);
        $errormessage = $options->getErrorMessage() ?: RuleMessage::getErrorMessage(Rules::ID_REQUIRED);
        if ((new TypeProxy($this))->hasRule(Rules::ID_REQUIRED)) {
            (new TypeProxy($this))->removeRule(Rules::ID_REQUIRED);
        }
        $rule = $this->prepareRule(Rules::required($errormessage), $options);
        $_this->state->setRequired(true, $rule);

        return $this;
    }

    // public function notEmptyString(Options $options = null)
    // {
    // 	$_this = $this->get_this();

    // 	$options = Helpers::getOptions($options);
    // 	$errormessage = $options->getErrormessage() ?: RuleMessage::getErrorMessage(Rules::ID_NOT_EMPTY_STRING);
    // 	$this->addRule(Rules::notEmptyString($errormessage), $options);
    // 	$_this->state->setNotEmptyString(true);
    // 	return $this;
    // }

    public function notNull(Options $options = null)
    {
        $_this = $this->get_this();

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
    /**
     * @param  Rule|Transformer|callable  $middleware
     * @param Options|null $options
     */
    private function addMiddleware($middleware, ?\Kedniko\Vivy\Core\Options $options = null): void
    {
        $_this = $this->get_this();

        /** @var LinkedList $linkedlist */
        $linkedlist = $_this->state->getMiddlewares();
        if ($options && $options->getAppendAfterCurrent()) {
            $linkedlist->appendAfterCurrent(new Node($middleware));
        } else {
            $linkedlist->append(new Node($middleware));
        }
        $_this->state->addMiddlewareId($middleware->getID());
    }

    private function get_this()
    {
        return $this instanceof TypeProxy ? $this->field : $this;
    }

    /**
     * @param  Rule|Transformer|callable  $middleware
     */
    protected function prependMiddleware($middleware)
    {
        $_this = $this->get_this();

        /** @var LinkedList $linkedlist */
        $linkedlist = $_this->state->getMiddlewares();
        $linkedlist->prepend(new Node($middleware));
        $_this->state->addMiddlewareId($middleware->getID());
    }

    private function removeMiddleware($middlewareid, $removeOnlyOne = false, $hardRemove = true): void
    {
        $type = $this;

        if ($type instanceof TypeProxy) {
            $type = $this->field;
        }

        if ($hardRemove) {
            /** @var LinkedList $linkedlist */
            $linkedlist = $type->state->getMiddlewares();
            $linkedlist->remove(function (Middleware $middleware) use ($middlewareid): bool {
                return $middleware->getID() === $middlewareid;
            }, $removeOnlyOne);
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
        $_this = $this->get_this();

        $_this->state->setStopOnFailure(true);

        return $this;
    }

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

    /**
     * @param  mixed  $ruleID
     * @param  bool  $hardRemove
     * HARD REMOVE: Remove from list and cache
     * SOFT REMOVE: remove only from cache (better performance)
     */
    public function removeRule($ruleID, $hardRemove = true)
    {
        $this->removeMiddleware($ruleID, false, $hardRemove);

        return $this;
    }

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

    /**
     * @param  Transformer|callable  $transformer Transformer or function name
     * @param  null  $args (optional) `...$args` arguments to pass to the function
     */
    public function addTransformer($transformer, Options $options = null)
    {
        $options = Helpers::getOptions($options);

        if (
            ! $transformer instanceof Transformer && ! is_string($transformer) && ! is_callable($transformer)
        ) {
            throw new VivyException('Expected type Transformer or function name', 1);
        }

        $args = array_slice(func_get_args(), 2);

        if (is_callable($transformer)) {
            if (is_string($transformer)) {
                $transformer = V::transformer($transformer, function (Context $c) use ($transformer) {
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
                });
            } else {
                $transformer = V::transformer('transformer', $transformer);
            }
        }

        $errormessage = $options->getErrorMessage();
        $stopOnFailure = $options->getStopOnFailure();

        if ($stopOnFailure !== null) {
            $transformer->setStopOnFailure($stopOnFailure);
        }
        if ($args !== null) {
            $transformer->setArgs($args);
        }

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

    /**
     * @param  callable|callable  $callback
     */
    public function addCallback($callback, Options $options = null)
    {
        $options = Helpers::getOptions($options);

        if (is_callable($callback)) {
            $callback = V::callback(null, $callback, $options);
        }
        $this->addMiddleware($callback);

        return $this;
    }

    /**
     * @param  string  $transformerID
     * @param  bool  $hardRemove
     * HARD REMOVE: Remove from list and cache
     * SOFT REMOVE: remove only from cache (better performance)
     */
    public function removeTransformer($transformerID, $hardRemove = true)
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

    // public function setErrorMessageRequired($errormessage)
    // {
    // 	$_this->state->getcustomErrMessages()[Rules::ID_REQUIRED] = $errormessage;
    // 	return $this;
    // }

    /**
     * @param  mixed|array  $rulesID
     * @param  mixed  $errormessage
     */
    public function setErrorMessage($rulesID, $errormessage)
    {
        $_this = $this->get_this();

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
     *
     * @param  string  $errormessage
     */
    public function setErrorMessageEmpty($errormessage)
    {
        $_this = $this->get_this();

        // $rulesID = [Rules::ID_REQUIRED, Rules::ID_NOT_EMPTY_STRING, Rules::ID_NOT_NULL];
        // foreach ($rulesID as $ruleID) {
        // 	$_this->state->getcustomErrMessages()[$ruleID] = $errormessage;
        // }
        $_this->state->setErrorMessageEmpty($errormessage);

        return $this;
    }

    /**
     * @param  string  $errormessage
     */
    public function setErrorMessageAny($errormessage)
    {
        $_this = $this->get_this();

        $_this->state->setErrorMessageAny($errormessage);

        return $this;
    }

    /**
     * @param  string  $errormessages
     * * Example:
     * [Rules::ID_REQUIRED=>'Error: required', Rules::ID_NOT_NULL=>'Error: notNull']
     */
    public function setErrorMessageFromArray($errormessages)
    {
        $_this = $this->get_this();

        $_this->state->setCustomErrMessages($errormessages);

        return $this;
    }

    // /**
    //  * Sostituisce un field con un altro
    //  * Nome ispirato dalla libreria Angular https://www.learnrxjs.io/learn-rxjs/operators/transformation/switchmap
    //  *
    //  */
    // public function switchMap($type){
    //
    // }

    // public function tap(callable $callback){
    // 	$this->addMiddleware($callback);
    // }

    /**
     * @param  callable  $callback fn(Context $c)
     */
    public function onValid(callable $callback)
    {
        $_this = $this->get_this();

        $_this->state->addOnValid($callback);

        return $this;
    }

    /**
     * @param  string|string[]|null  $rulesID
     */
    public function onError(callable $callback, $rulesID = null)
    {
        $_this = $this->get_this();

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

    // da decidere

    // public function removeOnValid() {
    // 	$this->onValid = null;
    // }
    // public function removeOnError() {
    // 	$this->onError = null;
    // 	return $this;
    // }

    public function setDefaultOnError($rulesID, $value)
    {
        $_this = $this->get_this();

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
     *
     * @param  mixed  $value
     */
    public function catchAll($value)
    {
        $this->state->setDefaultValuesAny($value);

        return $this;
    }

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

    public function getStopOnFailure()
    {
        if (!$this->state->hasStopOnFailure()) {
            return false;
        }
        return $this->state->getStopOnFailure() === true;
    }

    private function setName($name): void
    {
        $this->state->setName($name);
    }

    public function getName(): string|Undefined
    {
        return $this->state->getName();
    }

    /**
     $$\    $$\          $$\ $$\       $$\            $$\     $$\
     $$ |   $$ |         $$ |\__|      $$ |           $$ |    \__|
     $$ |   $$ |$$$$$$\  $$ |$$\  $$$$$$$ | $$$$$$\ $$$$$$\   $$\  $$$$$$\  $$$$$$$\
     \$$\  $$  |\____$$\ $$ |$$ |$$  __$$ | \____$$\\_$$  _|  $$ |$$  __$$\ $$  __$$\
     \$$\$$  / $$$$$$$ |$$ |$$ |$$ /  $$ | $$$$$$$ | $$ |    $$ |$$ /  $$ |$$ |  $$ |
     \$$$  / $$  __$$ |$$ |$$ |$$ |  $$ |$$  __$$ | $$ |$$\ $$ |$$ |  $$ |$$ |  $$ |
     \$  /  \$$$$$$$ |$$ |$$ |\$$$$$$$ |\$$$$$$$ | \$$$$  |$$ |\$$$$$$  |$$ |  $$ |
     \_/    \_______|\__|\__| \_______| \_______|  \____/ \__| \______/ \__|  \__|
     */

    /**
     * @param  mixed  $value
     * @return Validated
     */
    public function validate($value = null, Context $fatherContext = null)
    {
        $this->skipOtherMiddlewares = false;
        $this->skipOtherRules = false;
        $this->fieldProxy = new TypeProxy($this);
        $issetvalue = count(func_get_args());

        $this->context = $this->getContext($fatherContext);

        if ($issetvalue) {
            $this->context->value = $value;
        } elseif ($this->state->hasData()) {
            $this->context->value = $this->state->getData();
        } else {
            throw new \Exception('No data to validate');
        }
        unset($value);

        // CAN BE OPTIMIZED

        // if (!$this->fieldProxy->hasMiddlewares()) {
        //     return new Validated($this->value, []);
        // }

        $this->canBeEmptyString = $this->state->canBeEmptyString();
        $this->canBeNull = $this->state->canBeNull();

        if ($this instanceof TypeOr) {
            $orChildCanBeNull = $this->state->_extra['childCanBeNull'] ?? false;
            $orChildCanBeEmptyString = $this->state->_extra['childCanBeEmptyString'] ?? false;
            $emptyIsInvalid = (! $orChildCanBeEmptyString && $this->context->value === '') || (! $orChildCanBeNull && $this->context->value === null);
        } else {
            $emptyIsInvalid = (! $this->canBeEmptyString && $this->context->value === '') || (! $this->canBeNull && $this->context->value === null);
        }

        if ($emptyIsInvalid) {
            $this->getValidatedOnEmpty();
        } else {
            $this->applyMiddlewares();
        }

        $validated = new Validated($this->context->value, $this->context->errors);
        $validated->chain = $this;
        $this->validated = $validated;

        unset($this->fieldProxy, $this->skipOtherMiddlewares, $this->skipOtherRules, $this->canBeEmptyString, $this->canBeNull);

        return $validated;
    }

    private function applyMiddlewares(): void
    {
        /** @var LinkedList $middlewares */
        $middlewares = $this->fieldProxy->getMiddlewares();

        $areAllValid = true;
        $context = $this->context;

        while ($middlewares->hasNext()) {
            $middleware = $middlewares->getNext();

            if (! $middleware instanceof Middleware) {
                $middleware = Rules::equals($middleware, true);
            }

            /** @var Middleware $middleware */
            $skipThisMiddleware = $this->skipOtherMiddlewares || ($this->skipOtherRules && $middleware->isRule());

            if (! $skipThisMiddleware) {
                $idmiddleware = $middleware->getID();
                $middlewaresIds = $this->fieldProxy->getState()->getMiddlewaresIds();

                // OPTIMIZATION: if not bigger than 0 then it means that the middleware is not active
                if (! (isset($middlewaresIds[$idmiddleware]) && $middlewaresIds[$idmiddleware] > 0)) {
                    continue;
                }

                $context->setArgs($middleware->getArgs());
                $options = $middleware->getOptions();

                // check if condition

                if ($options->hasIf()) {
                    $ifCallback = $options->getIf();
                    if (is_callable($ifCallback)) {
                        if (! $ifCallback($context)) {
                            continue;
                        }
                    } elseif (! $ifCallback) {
                        continue;
                    }
                }

                $isValid = $this->applyMiddleware($middleware);

                if (! $isValid) {
                    $areAllValid = false;
                }

                if ($options->getOnce()) {
                    $middlewares->removeCurrent();
                    $this->state->removeMiddlewareId($idmiddleware);
                }
            }

            // if this is the last middleware
            // these callbacks could add more middlewares at runtime!
            // this is why we need to check this inside this while loop
            if (! $middlewares->hasNext() || $this->skipOtherMiddlewares) {
                if ($areAllValid) {
                    $this->applyOnValidFunctions();
                }
                if (! $areAllValid) {
                    $this->applyOnErrorFunctions();
                }
            }
        }

        $middlewares->rewind();
    }

    private function applyMiddleware(Middleware $middleware)
    {
        $errors = [];

        $fn = $middleware->getCallback();
        $middlewareId = $middleware->getID();

        // execute middleware callback
        if ($middleware instanceof Rule) {
            // ignore empty fields on rule
            $emptyIsValid = ($this->canBeEmptyString && $this->context->value === '') || ($this->canBeNull && $this->context->value === null);

            // $typeProxy = (new TypeProxy($this));
            // $isRequired = $typeProxy->isRequired();

            if ($emptyIsValid) {
                // if current middleware check force empty value, don't skip other rules but continue to validate...
                if (! in_array($middlewareId, [Rules::ID_NULL, Rules::ID_EMPTY_STRING])) {
                    $this->skipOtherRules = true;
                }
            } else {
                if (is_callable($fn)) {
                    $validated_or_bool = $fn($this->context);
                } else {
                    throw new VivyException('Function is invalid');
                }

                if ($validated_or_bool instanceof Validated) {
                    if ($this instanceof TypeOr) {
                        $childrenAreValid = ! ($this->state->_extra['or_errors'] ?? []);
                        $isvalid = $childrenAreValid;
                    } else {
                        $isvalid = $validated_or_bool->isValid();
                    }
                    $value = $validated_or_bool->value();
                } else {
                    $isvalid = $validated_or_bool;
                }

                if (! $isvalid) {
                    $newDefault = Helpers::tryToGetDefault($middleware->getID(), $this->fieldProxy, $this->context);
                    if (Helpers::isNotUndefined($newDefault)) {
                        $this->context->value = $newDefault;
                    } else {
                        $errors = Helpers::getErrors($middleware, $this->fieldProxy, $this->context);
                    }

                    if ($middleware->getStopOnFailure()) {
                        $this->skipOtherMiddlewares = true;
                    }
                }
            }
        } elseif ($middleware instanceof Transformer) {
            try {
                $value = $fn($this->context);
                if ($value instanceof Validated) {
                    $value = $value->value();
                }
                $this->context->value = $value;
            } catch (\Exception $e) {
                // Event::dispatch('vivy-transformation-failed', $th);

                $id = $middleware->getID();

                if ($this->fieldProxy->hasErrorMessageAny()) {
                    $this->context->errors['error'] = $this->fieldProxy->getErrorMessageAny();
                } else {
                    $this->context->errors[$id] = $this->fieldProxy->getCustomErrorMessage($id) ?: $middleware->getErrorMessage()
                        ?: TransformerMessage::getErrorMessage();
                }

                if ($middleware->getStopOnFailure()) {
                    $this->skipOtherMiddlewares = true;
                }
            }
        } elseif ($middleware instanceof Callback) {
            $this->context->errors = Helpers::issetOrDefault($this->_extra['or_errors'], []); // ??
            $fn = $middleware->getCallback();
            $fn($this->context);
        }

        // return $validated;

        if ($errors) {
            $errors = array_replace_recursive($this->context->errors, $errors);
            // $this->context->errors = $errors;

            $this->_extra['or_errors'] = $errors; // used by Callback() middleware in "applyMiddleware()"

            // errors for types inside orRule are ignored. The main orRule will handle them
            $canEditContextErrors = ! (isset($this->_extra['isInsideOr']) && $this->_extra['isInsideOr'] === true);
            if ($canEditContextErrors) {
                $this->context->errors = $errors;
            }
        }

        $isvalid = ! $errors;

        return $isvalid;
    }

    private function getArrayContext($index, $failsCount)
    {
        $c = new ArrayContext();
        $arrayContextProxy = new ContextProxy($c);
        $arrayContextProxy->setIndex($index);
        $arrayContextProxy->setExtra('failsCount', $failsCount);

        return $c;
    }

    /**
     * @param  mixed  $value
     * @param  array  $args
     * @return Context|ArrayContext|GroupContext
     */
    private function getContext(Context $fatherContext = null)
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

        (new ContextProxy($c))->setField($this);

        $c->setFatherContext($fatherContext);

        if ($c->rootContext() === null) {
            if ($fatherContext !== null) {
                $c->setRootContext($fatherContext->rootContext());
            } else {
                $c->setRootContext($c);
                $c->setIsRootContext(true);
            }
        }

        return $c;
    }

    protected function getOnValidFunctions()
    {
        return $this->state->getOnValid();
    }

    protected function applyOnValidFunctions()
    {
        foreach ($this->getOnValidFunctions() as $fn) {
            $fn($this->context);
        }
    }

    protected function getOnErrorFunctions()
    {
        return $this->state->getOnError();
    }

    /**
     * @param  Context  $c
     */
    protected function applyOnErrorFunctions()
    {
        $fns = $this->getOnErrorFunctions();

        if (isset($fns['all']) && $fnsAll = $fns['all']) {
            foreach ($fnsAll as $fnAll) {
                $fnAll($this->context);
            }
        }
        if (!isset($fns['rules'])) {
            return;
        }
        if (!($fnsRules = $fns['rules'])) {
            return;
        }
        foreach ($fnsRules as $ruleKey => $fnRuleArray) {
            if (array_key_exists($ruleKey, $this->context->errors)) {
                foreach ($fnRuleArray as $fnRule) {
                    $fnRule($this->context);
                }
            }
        }
    }

    /**
     * @return bool
     */
    public function fails()
    {
        return ! $this->isValid();
    }

    public function from($obj)
    {
        if ($obj) {
            $this->state = $obj->state; // share state
        }

        return $this;
    }

    /**
     * @param  bool  $value Overrides the old one if exists
     */
    public function failsWith($value)
    {
        return ! $this->isValidWith($value);
    }

    /**
     * @param  mixed  $value Overrides the old one if exists
     * @return array
     */
    public function errors($value = null)
    {
        if (count(func_get_args())) {
            $dataToValidate = $value;
        } elseif ($this->state->hasData()) {
            $dataToValidate = $this->state->getData();
        } else {
            throw new \Exception('No data to validate');
        }

        if (! $this->validated) {
            $this->validated = $this->validate($dataToValidate);
        }

        return $this->validated->errors();
    }

    /**
     * @return bool
     */
    public function isValid()
    {
        // if (count(func_get_args())) {
        //     $dataToValidate = $value;
        //     $this->state->setData($dataToValidate);
        // } else
        if ($this->state->hasData()) {
            $dataToValidate = $this->state->getData();
        } else {
            throw new \Exception('No data to validate');
        }

        // validate if has not already been validated
        if (! $this->validated) {
            $this->validated = $this->validate($dataToValidate);
        }

        return $this->validated->isValid();
    }

    /**
     * @param  mixed  $value
     * @return bool
     */
    public function isValidWith($value)
    {
        $dataToValidate = $value;
        $this->state->setData($dataToValidate);

        return $this->isValid();
    }

    private function getValidatedOnEmpty(): void
    {
        $errors = [];

        if (! $this->canBeEmptyString && $this->context->value === '') {
            $ruleID = Rules::ID_NOT_EMPTY_STRING;
            $rule = $this->fieldProxy->hasRule($ruleID) ? $this->fieldProxy->getRule($ruleID) : Rules::notEmptyString();

            $newDefault = Helpers::tryToGetDefault($rule->getID(), $this->fieldProxy, $this->context);
            if (Helpers::isNotUndefined($newDefault)) {
                $this->context->value = $newDefault;
            } else {
                $errors = Helpers::getErrors($rule, $this->fieldProxy, $this->context);
            }
        } elseif (! $this->canBeNull && $this->context->value === null) {
            $ruleID = Rules::ID_NOT_NULL;
            $rule = $this->fieldProxy->hasRule($ruleID) ? $this->fieldProxy->getRule($ruleID) : Rules::notNull();

            $newDefault = Helpers::tryToGetDefault($rule->getID(), $this->fieldProxy, $this->context);
            if (Helpers::isNotUndefined($newDefault)) {
                $this->context->value = $newDefault;
            } else {
                $errors = Helpers::getErrors($rule, $this->fieldProxy, $this->context);
            }
        }

        // $this->context->errors = array_replace_recursive($this->context->errors, $errors);

        $errors = array_replace_recursive($this->context->errors, $errors);
        // $this->context->errors = $errors;

        $this->_extra['or_errors'] = $errors; // used by Callback() middleware in "applyMiddleware()"

        // errors for types inside orRule are ignored. The main orRule will handle them
        $canEditContextErrors = ! (isset($this->_extra['isInsideOr']) && $this->_extra['isInsideOr'] === true);
        if ($canEditContextErrors) {
            $this->context->errors = $errors;
        }
    }

    /**
     * @param  callable|mixed  $callback_or_value
     */
    public function setValue($callback_or_value)
    {
        if (is_callable($callback_or_value)) {
            $callback = $callback_or_value;
        } else {
            $callback = function () use ($callback_or_value) {
                return $callback_or_value;
            };
        }
        $transformer = new Transformer(Rules::ID_SET_VALUE, $callback);
        $type = (new \Kedniko\Vivy\Types\Type())->from($this);
        $type->addTransformer($transformer);

        return $type;
    }
}
