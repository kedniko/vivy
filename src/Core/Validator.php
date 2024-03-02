<?php

namespace Kedniko\Vivy\Core;

use Kedniko\Vivy\Type;
use Kedniko\Vivy\Rules;
use Kedniko\Vivy\Callback;
use Kedniko\Vivy\Transformer;
use Kedniko\Vivy\Type\TypeOr;
use Kedniko\Vivy\Enum\RulesEnum;
use Kedniko\Vivy\Support\TypeProxy;
use Kedniko\Vivy\Contracts\TypeInterface;
use Kedniko\Vivy\Exceptions\VivyException;
use Kedniko\Vivy\Contracts\ContextInterface;
use Kedniko\Vivy\Messages\TransformerMessage;
use Kedniko\Vivy\Contracts\MiddlewareInterface;

final class Validator
{

    public TypeInterface $type;

    public ContextInterface|null $fatherContext;

    public ContextInterface $context;

    public Validated $validated;


    public function __construct(
        TypeInterface $type,
        ContextInterface|null $fatherContext = null,
    ) {
        $this->type = $type;
        $this->fatherContext = $fatherContext;
    }

    public function validate(mixed $value = null): Validated
    {
        $this->type->skipOtherMiddlewares = false;
        $this->type->skipOtherRules = false;
        $this->type->fieldProxy = new TypeProxy($this->type);
        $issetvalue = func_num_args();

        $this->context = $this->type->getContext($this->fatherContext);

        if ($issetvalue !== 0) {
            $this->context->value = $value;
        } elseif ($this->type->state->hasData()) {
            $this->context->value = $this->type->state->getData();
        } else {
            throw new \Exception('No data to validate');
        }
        unset($value);

        // CAN BE OPTIMIZED

        // if (!$this->type->fieldProxy->hasMiddlewares()) {
        //     return new Validated($this->type->value, []);
        // }


        $this->type->canBeEmptyString = $this->type->state->canBeEmptyString();
        $this->type->canBeNull = $this->type->state->canBeNull();

        if ($this->type instanceof TypeOr) {
            $orChildCanBeNull = $this->type->state->_extra['childCanBeNull'] ?? false;
            $orChildCanBeEmptyString = $this->type->state->_extra['childCanBeEmptyString'] ?? false;
            $emptyIsInvalid = (!$orChildCanBeEmptyString && $this->context->value === '') || (!$orChildCanBeNull && $this->context->value === null);
        } else {
            $emptyIsInvalid = (!$this->type->canBeEmptyString && $this->context->value === '') || (!$this->type->canBeNull && $this->context->value === null);
        }

        if (false && 'TODO' && $emptyIsInvalid) {
            $this->getValidatedOnEmpty();
        } else {
            $this->applyMiddlewares();
        }

        $validated = new Validated($this->context->value, $this->context->errors);
        $validated->chain = $this->type;
        $this->validated = $validated;

        unset($this->type->fieldProxy, $this->type->skipOtherMiddlewares, $this->type->skipOtherRules, $this->type->canBeEmptyString, $this->type->canBeNull);

        return $this->validated;
    }

    public function getValidatedOnEmpty(): void
    {
        $errors = [];

        if (!$this->type->canBeEmptyString && $this->context->value === '') {
            $ruleID = RulesEnum::ID_NOT_EMPTY_STRING->value;
            $rule = $this->type->fieldProxy->hasRule($ruleID) ? $this->type->fieldProxy->getRule($ruleID) : Rules::notEmptyString();

            $newDefault = Helpers::tryToGetDefault($rule->getID(), $this->type->fieldProxy, $this->context);
            if (Helpers::isNotUndefined($newDefault)) {
                $this->context->value = $newDefault;
            } else {
                $errors = Helpers::getErrors($rule, $this->type->fieldProxy, $this->context);
            }
        } elseif (!$this->type->canBeNull && $this->context->value === null) {
            $ruleID = RulesEnum::ID_NOT_NULL->value;
            $rule = $this->type->fieldProxy->hasRule($ruleID) ? $this->type->fieldProxy->getRule($ruleID) : Rules::notNull();

            $newDefault = Helpers::tryToGetDefault($rule->getID(), $this->type->fieldProxy, $this->context);
            if (Helpers::isNotUndefined($newDefault)) {
                $this->context->value = $newDefault;
            } else {
                $errors = Helpers::getErrors($rule, $this->type->fieldProxy, $this->context);
            }
        }

        // $this->context->errors = array_replace_recursive($this->context->errors, $errors);

        $errors = array_replace_recursive($this->context->errors, $errors);
        // $this->context->errors = $errors;

        $this->type->_extra['or_errors'] = $errors; // used by Callback() middleware in "applyMiddleware()"

        // errors for types inside orRule are ignored. The main orRule will handle them
        $canEditContextErrors = !(isset($this->type->_extra['isInsideOr']) && $this->type->_extra['isInsideOr'] === true);
        if ($canEditContextErrors) {
            $this->context->errors = $errors;
        }
    }

    public function errors(mixed $value = null): array
    {
        if (func_num_args() !== 0) {
            $dataToValidate = $value;
        } elseif ($this->type->state->hasData()) {
            $dataToValidate = $this->type->state->getData();
        } else {
            throw new \Exception('No data to validate');
        }

        if (!$this->validated) {
            $this->validated = $this->type->validate($dataToValidate);
        }

        return $this->validated->errors();
    }

    public function isValid(): bool
    {
        // if (count(func_get_args())) {
        //     $dataToValidate = $value;
        //     $this->state->setData($dataToValidate);
        // } else
        if ($this->type->state->hasData()) {
            $dataToValidate = $this->type->state->getData();
        } else {
            throw new \Exception('No data to validate');
        }

        // validate if has not already been validated
        $this->validated ??= $this->validate($dataToValidate);

        return $this->validated->isValid();
    }

    public function isValidWith(mixed $value): bool
    {
        $dataToValidate = $value;
        $this->type->state->setData($dataToValidate);

        return $this->isValid();
    }

    private function applyMiddlewares(): void
    {
        $middlewares = $this->type->fieldProxy->getMiddlewares();

        $areAllValid = true;


        while ($middlewares->hasNext()) {
            $middleware = $middlewares->getNext();

            if (!$middleware instanceof MiddlewareInterface) {
                $middleware = Rules::equals($middleware, true);
            }

            $skipThisMiddleware = $this->type->skipOtherMiddlewares || ($this->type->skipOtherRules && $middleware->isRule());
            assert($middleware instanceof MiddlewareInterface);

            if (!$skipThisMiddleware) {
                $idmiddleware = $middleware->getID();
                $middlewaresIds = $this->type->fieldProxy->getState()->getMiddlewaresIds();

                // OPTIMIZATION: if not bigger than 0 then it means that the middleware is not active
                if (!(isset($middlewaresIds[$idmiddleware]) && $middlewaresIds[$idmiddleware] > 0)) {
                    continue;
                }

                $this->context->setArgs($middleware->getArgs());
                $options = $middleware->getOptions();

                // check if condition

                if ($options->hasIf()) {
                    $ifCallback = $options->getIf();
                    if (is_callable($ifCallback)) {
                        if (!$ifCallback($this->context)) {
                            continue;
                        }
                    } elseif (!$ifCallback) {
                        continue;
                    }
                }

                $isValid = $this->applyMiddleware($middleware);

                if (!$isValid) {
                    $areAllValid = false;
                }

                if ($options->getOnce()) {
                    $middlewares->removeCurrent();
                    $this->type->state->removeMiddlewareId($idmiddleware);
                }
            }

            // if this is the last middleware
            // these callbacks could add more middlewares at runtime!
            // this is why we need to check this inside this while loop
            if (!$middlewares->hasNext() || $this->type->skipOtherMiddlewares) {
                if ($areAllValid) {
                    $this->applyOnValidFunctions();
                } else {
                    $this->applyOnErrorFunctions();
                }
            }
        }

        $middlewares->rewind();
    }

    private function applyRule(Rule $middleware): array
    {

        $errors = [];
        $fn = $middleware->getCallback();


        // ignore empty fields on rule
        $emptyIsValid = ($this->type->canBeEmptyString && $this->context->value === '') || ($this->type->canBeNull && $this->context->value === null);

        if ($emptyIsValid) {
            // if current middleware check force empty value, don't skip other rules but continue to validate...
            if (!in_array($middleware->getID(), [RulesEnum::ID_NULL->value, RulesEnum::ID_EMPTY_STRING->value])) {
                $this->type->skipOtherRules = true;
            }
        } else {
            if (is_callable($fn)) {
                $validated_or_bool = $fn($this->context);
            } else {
                throw new VivyException('Function is invalid');
            }

            if ($validated_or_bool instanceof Validated) {
                if ($this->type instanceof TypeOr) {
                    $isvalid = !($this->type->state->_extra['or_errors'] ?? []);
                } else {
                    $isvalid = $validated_or_bool->isValid();
                }
                $validated_or_bool->value();
            } else {
                $isvalid = !!$validated_or_bool;
            }

            if (!$isvalid) {
                $newDefault = Helpers::tryToGetDefault($middleware->getID(), $this->type->fieldProxy, $this->context);
                if (Helpers::isNotUndefined($newDefault)) {
                    $this->context->value = $newDefault;
                } else {
                    $errors = Helpers::getErrors($middleware, $this->type->fieldProxy, $this->context);
                }

                if ($middleware->getStopOnFailure()) {
                    $this->type->skipOtherMiddlewares = true;
                }
            }
        }

        return $errors;
    }

    private function applyTransformer(Transformer $middleware): void
    {


        try {
            $fn = $middleware->getCallback();

            $value = $fn($this->context);
            if ($value instanceof Validated) {
                $value = $value->value();
            }
            $this->context->value = $value;
        } catch (\Exception) {
            // Event::dispatch('vivy-transformation-failed', $th);

            $id = $middleware->getID();

            if ($this->type->fieldProxy->hasErrorMessageAny()) {
                $this->context->errors['error'] = $this->type->fieldProxy->getErrorMessageAny();
            } else {
                $this->context->errors[$id] = $this->type->fieldProxy->getCustomErrorMessage($id) ?: $middleware->getErrorMessage()
                    ?: TransformerMessage::getErrorMessage();
            }

            if ($middleware->getStopOnFailure()) {
                $this->type->skipOtherMiddlewares = true;
            }
        }
    }

    private function applyCallback(Callback $middleware): void
    {

        $fn = $middleware->getCallback();
        $this->context->errors = Helpers::issetOrDefault($this->type->_extra['or_errors'], []); // ??
        $fn = $middleware->getCallback();
        $fn($this->context);
    }

    private function applyMiddleware(MiddlewareInterface $middleware)
    {
        $errors = [];

        // execute middleware callback
        if ($middleware instanceof Rule) {
            $errors = $this->applyRule($middleware);
        } elseif ($middleware instanceof Transformer) {
            $this->applyTransformer($middleware);
        } elseif ($middleware instanceof Callback) {
            $this->applyCallback($middleware);
        }


        // return $validated;

        if ($errors !== []) {
            $errors = array_replace_recursive($this->context->errors, $errors);
            // $this->context->errors = $errors;

            $this->type->_extra['or_errors'] = $errors; // used by Callback() middleware in "applyMiddleware()"

            // errors for types inside orRule are ignored. The main orRule will handle them
            $canEditContextErrors = !(isset($this->type->_extra['isInsideOr']) && $this->type->_extra['isInsideOr'] === true);
            if ($canEditContextErrors) {
                $this->context->errors = $errors;
            }
        }

        return !$errors;
    }

    private function applyOnValidFunctions(): void
    {

        foreach ($this->getOnValidFunctions() as $fn) {
            $fn($this->context);
        }
    }

    private function getOnErrorFunctions()
    {
        return $this->type->state->getOnError();
    }

    private function getOnValidFunctions()
    {
        return $this->type->state->getOnValid();
    }

    private function applyOnErrorFunctions(): void
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
}
