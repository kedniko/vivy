<?php

namespace Kedniko\Vivy\Support;

use Kedniko\Vivy\Callback;
use Kedniko\Vivy\Contracts\MiddlewareInterface;
use Kedniko\Vivy\Contracts\TypeInterface;
use Kedniko\Vivy\Core\Helpers;
use Kedniko\Vivy\Core\Options;
use Kedniko\Vivy\Core\Rule;
use Kedniko\Vivy\Core\State;
use Kedniko\Vivy\Transformer;
use Kedniko\Vivy\V;

final class Util
{
    public static function runFunction($fn, $parameters = [])
    {
        if (!$fn) {
            throw new \Exception('Invalid register function', 1);
        }
        if (is_callable($fn)) {
            return call_user_func_array($fn, $parameters);
        }
        $result = Helpers::getClassAndMethod($fn);
        if (!$result) {
            throw new \Exception('Invalid register function', 1);
        }
        $class = $result[0];
        $method = $result[1];
        if (is_string($class) && class_exists($class)) {
            $class = ltrim($class, '\\');
            $reflection = new \ReflectionClass($class);

            $methods = $reflection->getMethods(\ReflectionMethod::IS_PUBLIC);
            foreach ($methods as $a) {
                if (ltrim($a->class, '\\') === $class && $a->name === $method) {
                    try {
                        return $a->invoke(null);
                    } catch (\ReflectionException) {
                        return $a->invoke(new $class(), ...$parameters);
                    }
                }
            }
        } elseif (class_exists($class)) {
            return $class->$method(...$parameters);
        }
    }

    /**
     * @return class-string<object>[]
     */
    private static function getParentsClasses($class): array
    {
        $classes = [];
        $reflection = new \ReflectionClass($class);
        while ($reflection = $reflection->getParentClass()) {
            $classes[] = $reflection->getName();
        }

        return $classes;
    }

    public static function getMethodParameters($class, $method): array
    {
        $method = new \ReflectionMethod($class, $method);
        $parameters = $method->getParameters();

        return array_map(function ($parameter): array {
            $parameterType = $parameter->getType();
            $parameterTypeName = $parameterType->getName();

            return [
                'name' => $parameter->getName(),
                'type' => $parameterTypeName,
                'isBuiltin' => $parameterType->isBuiltin(),
                'allowsNull' => $parameter->allowsNull(),
            ];
        }, $parameters);
    }

    public static function handleUserDefinedCall($className, $methodName, $callerObj, $parameters)
    {
        $newField = null;
        $registered = V::$magicCaller->toArray();
        $originalCallerObj = $callerObj;

        if ($callerObj instanceof V) {
            $callerObj = null;
        }

        if (!isset($registered[$className][$methodName])) {

            // try to find the method in parent classes

            $classes = self::getParentsClasses($className);
            $found = false;
            foreach ($classes as $classnameparent) {
                if (isset($registered[$classnameparent][$methodName])) {
                    $found = true;
                    $className = $classnameparent;
                    break;
                }
            }
            if (!$found) {
                $id = $className . '::' . $methodName;
                throw new \Exception('Method "' . $id . '" does not exists. Throwed in ' . self::class, 1);
            }
        }

        $setup = $registered[$className][$methodName];
        $returntype = $setup['returnType'];
        $availableForType = $setup['availableForType'];

        // $parameters_for_callback = [$callerObj ?? null, ...$parameters];
        // if ($callerObj) {
        // 	$closure = \Closure::fromCallable($setup['function']);
        // 	$result = \Closure::bind($closure, $callerObj)();
        // // $result = call_user_func_array([$callerObj, $setup['function'][1]], $parameters_for_callback);
        // } else {
        // 	$result = call_user_func_array($setup['function'], $parameters_for_callback);
        // }

        $result = Util::runFunction($setup['function'], $parameters);
        // $result = call_user_func_array($setup['function'], $parameters);

        if (!$callerObj && !($result instanceof TypeInterface)) {
            if ($availableForType === V::class) {
                //
            } else {
                $callerObj = new $returntype();
            }
        }

        if (is_callable($result)) {
            // $c = new Context();
            // (new ContextProxy($c))->setField($callerObj);
            $result = $result($callerObj);
        }

        if ($result instanceof MiddlewareInterface) {
            $middleware = $result;
            $options = $middleware->getOptions();

            $args = [];
            foreach ($parameters as $arg) {
                $args[] = $arg;
                if ($arg instanceof Options) {
                    $options = $arg; // override old options
                    break; // options must be the last argument
                }
            }

            $options = Helpers::getOptions($options);

            if ($options->getErrorMessage()) {
                $middleware->setErrorMessage($options->getErrorMessage());
            }

            $options->setArgs($args);
            if ($middleware instanceof Rule) {
                $type = $callerObj->addRule($middleware, $options);
            } elseif ($middleware instanceof Transformer) {
                $type = $callerObj->addTransformer($middleware, $options);
            } elseif ($middleware instanceof Callback) {
                $type = $callerObj->addCallback($middleware, $options);
            } else {
                throw new \Exception('Unknown middleware type', 1);
            }

            $newField = new $returntype();
            $newField->state = $type->state;
        } elseif ($result instanceof TypeInterface) {
            $newField = $result;
            // if ($callerObj) {
            // 	$newField->state = $callerObj->state;
            // }
        }
        // continua
        // elseif ($result === null) {
        // 	$returntype = $setup['returnType'];
        // 	/** @var TypeInterface */
        // 	$newField = new $returntype();
        // 	$newField->state = $type->state;
        // }

        /** @var TypeInterface $newField */


        $newField->state ??= new State();
        $newField->state->_extra ??= [];
        $newField->state->_extra['caller'] = $className;

        /**
         * example:
         * $v = V::new();
         * $v->setfailHandler($fn);
         * $v->group([]); // transfer failHandler to group state
         */
        if ($originalCallerObj instanceof V) {
            (new Invader($originalCallerObj))->transfer($newField);
        }

        return $newField;
    }

    public static function clone($var)
    {
        return is_array($var) ? array_map(fn ($var) => self::clone($var), $var) : (is_object($var) ? clone $var : $var);
    }

    public static function classImplements(string $classname, string $interface): bool
    {
        $interfaces = class_implements($classname);

        return in_array($interface, $interfaces);
    }

    public static function basePath($path = ''): string
    {
        $p = __DIR__ . '/../../' . str_replace(['/', '\\'], DIRECTORY_SEPARATOR, ltrim((string) $path, '/'));
        return realpath($p) ?: $p;
    }

    public static function fileContent(string $path = '')
    {
        $p = self::basePath($path);
        if (file_exists($p)) {
            return include $p;
        }
        return null;
    }
}
