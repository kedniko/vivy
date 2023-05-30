<?php

namespace Kedniko\Vivy;

use Kedniko\Vivy\Call\TraitUserDefinedCallStatic;
use Kedniko\Vivy\Commands\ScanCommand;
use Kedniko\Vivy\Core\Args;
use Kedniko\Vivy\Core\Helpers;
use Kedniko\Vivy\Core\Middleware;
use Kedniko\Vivy\Core\Options;
use Kedniko\Vivy\Core\Rule;
use Kedniko\Vivy\Core\Validated;
use Kedniko\Vivy\Interfaces\VivyPlugin;
use Kedniko\Vivy\Messages\RuleMessage;
use Kedniko\Vivy\Plugins\Builtin;
use Kedniko\Vivy\Plugins\StandardLibrary\TypeAny;
use Kedniko\Vivy\Support\Arr;
use Kedniko\Vivy\Types\Type;

class V
{
    use TraitUserDefinedCallStatic;

    // const BASE = 'VIVY_BASE';

    /**
     * @var callable
     */
    public static $failHandler = null;

    private static $bootstrapped = false;

    /**
     * @var array
     */
    public static $registeredMiddlewares = [];

    private static $registered = [];

    /** @var Type */
    private static $currentType = null;

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
    public static function scan($registerFunction = null, $exportFile = null)
    {
        (new ScanCommand())->handle($exportFile);
    }

    // /**
    //  * @return Type
    //  */
    // public static function useBuilder()
    // {
    // 	return self::$currentType;
    // }

    public static function rule($ruleID, callable $ruleFn, $errormessage = null)
    {
        return new Rule($ruleID, $ruleFn, $errormessage);
    }

    /**
     * @param  mixed  $transformerID
     * @param  callable  $transformerFn `fn(Context){...}`
     */
    public static function transformer($transformerID, callable $transformerFn, Options $options = null)
    {
        $options = Options::build($options, func_get_args());

        return new Transformer($transformerID, $transformerFn, $options->getErrorMessage());
    }

    public static function callback($id, callable $callbackFn, Options $options = null)
    {
        $options = Options::build($options, func_get_args());

        return new Callback($id, $callbackFn, $options->getErrorMessage());
    }

    public static function args(array $args = [])
    {
        return new Args($args);
    }

    public static function registerPlugin(VivyPlugin $classname)
    {
        $classname->register();
        // if (Util::classImplements($classname, VivyPlugin::class)) {
        // 	$classname::register();
        // 	}
    }

    public static function register($setups)
    {
        $args = func_get_args();
        if (!count($args)) {
            return;
        }

        if (count($args) === 1) {
            // multiple setups

            if (is_array($args[0])) {
                // array of setups
                self::registerMany($args[0]);

                return;
            } else {
                $obj = $args[0];
                self::registerPlugin($obj);
            }
        } else {
            // one setup
            $availableForTypes = isset($args[0]) ? $args[0] : [];
            $availableForTypes = Arr::wrap($availableForTypes);

            // V::BASE experiment
            // foreach ($availableForTypes as $i => $availableForType) {
            // 	if ($availableForType === V::BASE) {
            // 		$availableForTypes[] = \Kedniko\Vivy\V::class;
            // 		$availableForTypes[] = \Kedniko\Vivy\Types\Type::class;
            // 		unset($availableForTypes[$i]);
            // 	}
            // }

            $availableForTypes = array_values(array_unique($availableForTypes));

            $methodName = $args[1];
            $function_or_class = isset($args[2]) ? $args[2] : null;
            $returnType = isset($args[3]) ? $args[3] : null;
            self::registerOne($methodName, $function_or_class, $availableForTypes, $returnType);
        }
    }


    protected static function registerOne(string $methodName, $middleware, array $availableForTypes = [], $returnType = null)
    {
        if ($middleware instanceof Middleware) {
            $function_or_class = function () use ($middleware) {
                return $middleware;
            };
        } elseif (is_bool($middleware)) {
            $bool = $middleware;
            $function_or_class = function () use ($bool) {
                return self::rule('::', function () use ($bool) {
                    return $bool;
                });
            };
        } else {
            $function_or_class = $middleware;
        }

        // if (is_array($function_or_class)) {
        // 	$function_or_class = $function_or_class[0] . '::' . $function_or_class[1];
        // }

        // if (is_callable($function)) {
        //     $function = $function();
        // }

        // if (!$function instanceof Middleware) {
        //     throw new \Exception('This is not a midleware', 1);
        // }

        if (!$returnType) {
            $returnType = $returnType ?? Type::class;
        }

        foreach ($availableForTypes as $avForType) {
            $classMethod = $avForType . '::' . $methodName;
            self::$registeredMiddlewares[$classMethod] = [
                'methodName' => $methodName,
                'function' => $function_or_class,
                'availableForType' => $avForType,
                'returnType' => $returnType,
            ];
        }
    }

    private static function registerMany($setups)
    {
        foreach ($setups as $key => $setup) {
            if (count($setup) === 3) {
                $methodName = $key;
                $callback = $setup[0];
                $availableForType = isset($setup[1]) ? $setup[1] : [];
                $returnType = isset($setup[2]) ? $setup[2] : null;
            } elseif (count($setup) === 4) {
                $methodName = $setup[0];
                $callback = $setup[1];
                $availableForType = isset($setup[2]) ? $setup[2] : [];
                $returnType = isset($setup[3]) ? $setup[3] : null;
            }

            V::register($methodName, $callback, $availableForType, $returnType);
        }
    }

    protected static function registerClass($class)
    {
        self::register($class);
        self::$registered = [
            'email' => [Builtin::class, 'email'],
        ];
    }

    /**
     * @param  Middleware|Middleware[]  $middlewares
     */
    public static function registerMiddleware($middlewares, $overwriteExisting = false)
    {
        if (!is_array($middlewares)) {
            $middlewares = [$middlewares];
        }

        foreach ($middlewares as $middleware) {
            if (!$middleware instanceof Middleware) {
                return;
            }

            $id = $middleware->getID();

            if (!$overwriteExisting && array_key_exists($id, self::$registeredMiddlewares)) {
                throw new \Exception("Middleware \"{$id}\" already exists in this application", 1);
            }
            self::$registeredMiddlewares[$id] = $middleware;

            // if ($middleware instanceof Rule) {
            // 	if (!$overwriteExisting && array_key_exists($id, self::$registeredMiddlewares)) {
            // 		throw new \Exception("Middleware \"{$id}\" already exists in this application", 1);
            // 	}
            // 	self::$registeredMiddlewares[$id] = $middleware;
            // } else if ($middleware instanceof Transformer) {
            // 	if (!$overwriteExisting && array_key_exists($id, self::$registeredMiddlewares)) {
            // 		throw new \Exception("Middleware \"{$id}\" already exists in this application", 1);
            // 	}
            // 	self::$registeredMiddlewares[$id] = $middleware;
            // } else if ($middleware instanceof Callback) {
            // 	if (!$overwriteExisting && array_key_exists($id, self::$registeredMiddlewares)) {
            // 		throw new \Exception("Middleware \"{$id}\" already exists in this application", 1);
            // 	}
            // 	self::$registeredMiddlewares[$id] = $middleware;
            // }
        }
    }

    public static function hasFailHandler($id = 'default')
    {
        return isset(self::$failHandler[$id]);
    }

    public static function getFailHandler($id = 'default')
    {
        return self::$failHandler[$id];
    }

    public static function setFailHandler(string $id, callable $handler)
    {
        if (is_callable($handler)) {
            self::$failHandler[$id] = $handler;
        }
    }

    public static function issetVar(&$variable, $varname, $errormessage = null)
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

    public static function issetVarOrDefault(&$variable, $defaultValue)
    {
        return Helpers::issetOrDefault($variable, $defaultValue);
    }

    /**
     * @param  array  $array
     * @param  mixed  $path dot.separated.path
     * @param  null  $errormessage
     * @return Validated
     */
    public static function issetVarPath($array, $path, $errormessage = null)
    {
        if (!Arr::get($array, $path)) {
            $chunks = explode('.', $path);
            $varname = end($chunks);
            if ($errormessage && is_callable($errormessage)) {
                $c = (new Context())->setArgs(func_get_args());
                $errormessage = $errormessage($c);
            }
            $errors = Arr::set($array, $path . '.required', $errormessage ?: "{$varname} is not set");

            return new Validated(null, $errors);
        }

        return new Validated(Arr::get($array, $path), []);
    }

    /**
     * @param  array  $array
     * @param  mixed  $path dot.separated.path
     * @param  mixed  $defaultValue
     * @return mixed
     */
    public static function issetVarPathOrDefault($array, $path, $defaultValue)
    {
        if (Arr::get($array, $path)) {
            return $array;
        }

        return Arr::set($array, $path, $defaultValue);
    }

    /**
     * @param  bool  $bool
     * @param  string  $name
     * @param  null  $errormessage
     * @return Validated
     */
    public static function assertTrue($bool, $name, $errormessage = null)
    {
        if (!$bool) {
            $errormessage = $errormessage ?: 'Assertion failed';

            return new Validated(null, [$name => $errormessage]);
        }

        return new Validated(true, []);
    }

    public static function assertTrueOrDefault($bool, $defaultValue)
    {
        return $bool === true ? true : $defaultValue;
    }

    // /**
    //  * @param string $methodName
    //  * @param mixed ...$args — [optional]
    //  */
    // public static function call($methodName, $args = null)
    // {
    //     $args = func_get_args();

    //     // remove methodName
    //     array_shift($args);

    //     return self::handleUserDefinedCall($methodName, $args);
    // }

    private static function addRule(Rule $rule, Options $options)
    {
        if ($options->getStopOnFailure() !== null && !is_bool($options->getStopOnFailure())) {
            throw new \InvalidArgumentException('$stopOnFailure must be of type bool|null in rule "' . $rule->getID() . '"', 1);
        }

        if ($options->getStopOnFailure() !== null) {
            $rule->setStopOnFailure($options->getStopOnFailure());
        } else {
            $rule->setStopOnFailure(true);
        }

        if ($options->getArgs() !== null) {
            $rule->setArgs($options->getArgs());
        }

        $type = new Type();
        $type->addRule($rule, $options);

        return $type;
    }

    /**
     * Remove this rule after it has been used the first time
     */
    public static function optional()
    {
        $type = new TypeAny();
        $type->state->requiredIf = false;

        return $type;
    }

    public static function requiredIf($value)
    {
        $type = new TypeAny();
        $type->state->requiredIf = $value;

        return $type;
    }

    public static function requiredIfField($fieldname, $value)
    {
        $type = new TypeAny();
        $getContextFn = function (Context $c) use ($fieldname) {
            $fieldContext = $c->fatherContext->getFieldContext($fieldname);

            return $fieldContext;
        };
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
}
