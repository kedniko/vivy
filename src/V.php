<?php

namespace Kedniko\Vivy;

use Kedniko\Vivy\Commands\ScanCommand;
use Kedniko\Vivy\Contracts\ContextInterface;
use Kedniko\Vivy\Contracts\MiddlewareInterface;
use Kedniko\Vivy\Core\Args;
use Kedniko\Vivy\Core\hasMagicCallStatic;
use Kedniko\Vivy\Core\Helpers;
use Kedniko\Vivy\Core\Middleware;
use Kedniko\Vivy\Core\Options;
use Kedniko\Vivy\Core\Rule;
use Kedniko\Vivy\Core\Validated;
use Kedniko\Vivy\Interfaces\VivyPlugin;
use Kedniko\Vivy\Messages\RuleMessage;
use Kedniko\Vivy\Plugin\Standard\Rules;
use Kedniko\Vivy\Plugin\Standard\TypeAny;
use Kedniko\Vivy\Support\Arr;
use Kedniko\Vivy\Support\MagicCaller;
use Kedniko\Vivy\Support\Registrar;

final class V
{
    use hasMagicCallStatic;

    // const BASE = 'VIVY_BASE';

    /**
     * @var callable
     */
    public static $failHandler;

    public static ?MagicCaller $magicCaller = null;

    /**
     * Private constructor
     */
    // private function __construct()
    // {
    // }

    /**
     * @param  callable  $registerFunction
     * @param  null  $exportFile The file will be overwritten if it exists
     * @param  bool  $auto
     */
    public static function scan($registerFunction = null, $exportFile = null): void
    {
        (new ScanCommand())->handle();
    }

    public static function rule(string $ruleID, callable $ruleFn, $errormessage = null): Rule
    {
        return new Rule($ruleID, $ruleFn, $errormessage);
    }

    /**
     * @param  callable  $transformerFn `fn(Context){...}`
     */
    public static function transformer(string $transformerID, callable $transformerFn, Options $options = null): Transformer
    {
        $options = Options::build($options, func_get_args());

        return new Transformer($transformerID, $transformerFn, $options->getErrorMessage());
    }

    public static function callback(string $id, callable $callbackFn, Options $options = null): Callback
    {
        $options = Options::build($options, func_get_args());

        return new Callback($id, $callbackFn, $options->getErrorMessage());
    }

    public static function args(array $args = []): Args
    {
        return new Args($args);
    }

    public static function registerPlugin(VivyPlugin $classname): void
    {
        $classname->register();
    }

    private static function initMagicCaller(): void
    {
        self::$magicCaller ??= new MagicCaller();
    }

    public static function registerMany(array $setups): void
    {
        foreach ($setups as $setup) {
            V::register($setup);
        }
    }

    public static function register(Registrar $registrar): void
    {
        if ($registrar->callback instanceof MiddlewareInterface) {
            $function_or_class = fn (): array|string|null => $registrar->callback;
        } elseif (is_bool($registrar->callback)) {
            $bool = $registrar->callback;
            $function_or_class = fn (): \Kedniko\Vivy\Core\Rule => self::rule('::', fn (): bool => $bool);
        } else {
            $function_or_class = $registrar->callback;
        }

        $returnType = $registrar->return;

        self::initMagicCaller();

        foreach (Arr::wrap($registrar->for) as $availableForType) {
            $methodName = $registrar->id;
            $id = $availableForType . '::' . $methodName;

            self::$magicCaller->register(
                $id,
                $methodName,
                $function_or_class,
                $availableForType,
                $returnType,
            );
        }
    }

    /**
     * @param  Middleware|Middleware[]  $middlewares
     */
    public static function registerMiddleware(
        MiddlewareInterface|array $middlewares,
        bool $overwriteExisting = false
    ) {
        if (!is_array($middlewares)) {
            $middlewares = [$middlewares];
        }

        foreach ($middlewares as $middleware) {
            if (!$middleware instanceof MiddlewareInterface) {
                return;
            }

            $id = $middleware->getID();

            if (!$overwriteExisting && self::$magicCaller->hasId($id)) {
                throw new \Exception("Middleware \"{$id}\" already exists in this application", 1);
            }
            self::$magicCaller->addToId($id, $middleware);
        }
    }

    public static function hasFailHandler(string $id = 'default')
    {
        return isset(self::$failHandler[$id]);
    }

    public static function getFailHandler(string $id = 'default')
    {
        return self::$failHandler[$id];
    }

    public static function setFailHandler(string $id, callable $handler): void
    {
        self::$failHandler[$id] = $handler;
    }

    public static function issetVar(&$variable, string $varname, string $errormessage = null): Validated
    {
        if (!isset($variable)) {
            return new Validated(null, [
                $varname => [
                    Rules::ID_REQUIRED => $errormessage ?: RuleMessage::getErrorMessage(Rules::ID_REQUIRED),
                ],
            ]);
        }

        return new Validated($variable, []);
    }

    public static function issetVarOrDefault(mixed &$variable, mixed $defaultValue)
    {
        return Helpers::issetOrDefault($variable, $defaultValue);
    }

    /**
     * @param  mixed  $path dot.separated.path
     * @param  null  $errormessage
     */
    public static function issetVarPath(
        array $array,
        string|int $path,
        string $errormessage = null
    ): Validated {
        if (!Arr::get($array, $path)) {
            $chunks = explode('.', (string) $path);
            $varname = end($chunks);
            if ($errormessage && is_callable($errormessage)) {
                $c = (new \Kedniko\Vivy\Context())->setArgs(func_get_args());
                $errormessage = $errormessage($c);
            }
            $errors = Arr::set($array, $path . '.required', $errormessage ?: "{$varname} is not set");

            return new Validated(null, $errors);
        }

        return new Validated(Arr::get($array, $path), []);
    }

    /**
     * @param  mixed  $path dot.separated.path
     * @return mixed
     */
    public static function issetVarPathOrDefault(array $array, mixed $path, mixed $defaultValue)
    {
        if (Arr::get($array, $path)) {
            return $array;
        }

        return Arr::set($array, $path, $defaultValue);
    }

    /**
     * @param  null  $errormessage
     */
    public static function assertTrue(bool $bool, string $name, string $errormessage = null): Validated
    {
        if (!$bool) {
            $errormessage = $errormessage ?: 'Assertion failed';

            return new Validated(null, [$name => $errormessage]);
        }

        return new Validated(true, []);
    }

    public static function assertTrueOrDefault(bool $bool, $defaultValue)
    {
        return $bool ? true : $defaultValue;
    }

    /**
     * Remove this rule after it has been used the first time
     */
    public static function optional(): TypeAny
    {
        $type = new TypeAny();
        $type->state->requiredIf = false;

        return $type;
    }

    public static function requiredIf(mixed $value): TypeAny
    {
        $type = new TypeAny();
        $type->state->requiredIf = $value;

        return $type;
    }

    public static function requiredIfField(string $fieldname, mixed $value): TypeAny
    {
        $type = new TypeAny();
        $getContextFn = fn (ContextInterface $c) => $c->fatherContext->getFieldContext($fieldname);
        $type->state->requiredIfField = [
            'fieldname' => $fieldname,
            'value' => $value,
            'getContextFn' => $getContextFn,
        ];

        return $type;
    }

    // public static function registerCustom($name)
    // {
    // 	return new Registrar($name);
    // }

    // /**
    //  * @return Type
    //  */
    // public static function useBuilder()
    // {
    // 	return self::$currentType;
    // }
}
