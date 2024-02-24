<?php

namespace Kedniko\Vivy\Contracts;

use Closure;
use Kedniko\Vivy\Callback;
use Kedniko\Vivy\Core\Rule;
use Kedniko\Vivy\Core\State;
use Kedniko\Vivy\Transformer;
use Kedniko\Vivy\ArrayContext;
use Kedniko\Vivy\Core\Options;
use Kedniko\Vivy\Core\Undefined;
use Kedniko\Vivy\Core\Validated;
use Kedniko\Vivy\Traits\Typeable;
use Kedniko\Vivy\Core\GroupContext;
use Kedniko\Vivy\Support\TypeProxy;
use Kedniko\Vivy\Contracts\ContextInterface;

interface TypeInterface
{

    public function required(Options $options = null);

    public function notNull(Options $options = null);

    public function once(): void;

    public function addRule(Rule $rule, Options $options = null);

    public function stopOnFailure();

    public function removeRule(string $ruleID, bool $hardRemove = true);

    public function addTransformer(Transformer|callable|string $transformer, Options $options = null);

    public function tap(callable $callback);

    public function addCallback(Callback $callback, Options $options = null);

    public function removeTransformer(string $transformerID, bool $hardRemove = true);

    public function setErrorMessage(string|array $rulesID, mixed $errormessage);

    public function setErrorMessageEmpty(string $errormessage);

    public function setErrorMessageAny(string $errormessage);

    public function setErrorMessageFromArray(array $errormessages);

    public function onValid(callable $callback);

    public function onError(callable $callback, string|array $rulesID = null);

    public function setDefaultOnError($rulesID, $value);

    public function catchAll(mixed $value);

    public function getStopOnFailure();

    public function getName(): string|Undefined;

    public function getContext(ContextInterface $fatherContext = null): ContextInterface|ArrayContext|GroupContext;

    public static function shareState(TypeInterface $from, TypeInterface $to);

    public static function new(Typeable|TypeInterface|null $from = null);

    public function from(?TypeInterface $obj);

    public function validate(mixed $value = null, ContextInterface $fatherContext = null): Validated;

    public function errors(mixed $value = null): array;

    public function isValid(): bool;

    public function fails(): bool;

    public function isValidWith(mixed $value): bool;

    public function failsWith(mixed $value): bool;

    public function setValue(mixed $callback_or_value);
}
