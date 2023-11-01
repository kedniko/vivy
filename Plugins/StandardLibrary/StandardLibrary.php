<?php

namespace Kedniko\Vivy\Plugins\StandardLibrary;

use Kedniko\Vivy\Core\Options;
use Kedniko\Vivy\Interfaces\VivyPlugin;
use Kedniko\Vivy\Messages\RuleMessage;
use Kedniko\Vivy\Support\Registrar;
use Kedniko\Vivy\Support\TypeProxy;
use Kedniko\Vivy\Type;
use Kedniko\Vivy\V;

final class StandardLibrary implements VivyPlugin
{
    public function register(): void
    {
        // V::register([
        // 	// [availableFor, name, callback, return],
        // 	[V::class, 'any', [self::class, 'any'], Type::class],

        // 	[[V::class, TypeAny::class], 'or', [self::class, 'or'], TypeOr::class],
        // 	[[V::class, TypeAny::class], 'group', [self::class, 'group'], TypeGroup::class],
        // 	[[TypeString::class], 'digits', [self::class, 'digits'], TypeString::class],
        // 	[[V::class, TypeString::class], 'date', [self::class, 'date'], TypeStringDate::class],
        // ]);
        // return;

        V::registerMany([
            Registrar::make('any')->for(V::class)->callback([self::class, 'any'])->return(Type::class),

            // [[availableFor], name, callback, return],
            // [V::class, 'any', [self::class, 'any'], Type::class],

            Registrar::make('or')->for([V::class, TypeAny::class, Type::class])->callback([self::class, 'or'])->return(TypeOr::class),
            Registrar::make('group')->for([V::class, TypeAny::class])->callback([self::class, 'group'])->return(TypeGroup::class),

            Registrar::make('file')->for([V::class, TypeAny::class])->callback([self::class, 'file'])->return(TypeFile::class),
            Registrar::make('int')->for([V::class, TypeAny::class])->callback([self::class, 'int'])->return(TypeInt::class),
            Registrar::make('bool')->for([V::class, TypeAny::class])->callback([self::class, 'bool'])->return(TypeBool::class),
            Registrar::make('float')->for([V::class, TypeAny::class])->callback([self::class, 'float'])->return(TypeFloat::class),
            Registrar::make('number')->for([V::class, TypeAny::class])->callback([self::class, 'number'])->return(TypeNumber::class),
            // [[V::class, TypeAny::class], 'make', [self::class, 'make'], TypeMake::class], //
            // [[V::class, TypeAny::class], 'everything', [self::class, 'everything'], TypeEverything::class], //
            // [[V::class, TypeAny::class], 'notFalsy', [self::class, 'notFalsy'], TypeNotFalsy::class], //
            // [[V::class, TypeAny::class], 'notNull', [self::class, 'notNull'], TypeNotNull::class], //
            Registrar::make('null')->for([V::class, TypeAny::class])->callback([self::class, 'null'])->return(TypeNull::class),
            Registrar::make('files')->for([V::class, TypeAny::class])->callback([self::class, 'files'])->return(TypeFiles::class),
            Registrar::make('array')->for([V::class, TypeAny::class])->callback([self::class, 'array'])->return(TypeArray::class),

            Registrar::make('string')->for([V::class, TypeAny::class])->callback([self::class, 'string'])->return(TypeString::class),
            Registrar::make('date')->for([V::class, TypeString::class, TypeAny::class])->callback([self::class, 'date'])->return(TypeStringDate::class),
            Registrar::make('intString')->for([V::class, TypeAny::class])->callback([self::class, 'intString'])->return(TypeStringInt::class),
            Registrar::make('boolString')->for([V::class, TypeAny::class])->callback([self::class, 'boolString'])->return(TypeStringBool::class),
            Registrar::make('digits')->for([TypeString::class])->callback([self::class, 'digits'])->return(TypeString::class),
            Registrar::make('floatString')->for([V::class, TypeAny::class])->callback([self::class, 'floatString'])->return(TypeStringFloat::class),
            Registrar::make('numberString')->for([V::class, TypeAny::class])->callback([self::class, 'numberString'])->return(TypeStringNumber::class),
            Registrar::make('email')->for([V::class, TypeAny::class])->callback([self::class, 'email'])->return(TypeStringEmail::class),
            Registrar::make('emptyString')->for([V::class, TypeAny::class])->callback([self::class, 'emptyString'])->return(TypeStringEmpty::class),
            Registrar::make('notEmptyString')->for([V::class, TypeAny::class])->callback([self::class, 'notEmptyString'])->return(TypeString::class),
            // [V::class, 'inArray', [Rules::class, 'inArray'], Type::class],
            // [V::class, 'notInArray', [Rules::class, 'notInArray'], Type::class],
            // [V::class, 'equals', [Rules::class, 'equals'], Type::class],

            // [V::class, 'optional', [self::class, 'optional'], Type::class], // TODO
            Registrar::make('undefined')->for([V::class, TypeAny::class])->callback([self::class, 'undefined'])->return(TypeUndefined::class),

            // casts
            Registrar::make('asAny')->for([V::class, TypeAny::class, Type::class])->callback([self::class, 'asAny'])->return(TypeAny::class),
            Registrar::make('asArray')->for([V::class, TypeAny::class, Type::class])->callback([self::class, 'asArray'])->return(TypeArray::class),
            Registrar::make('asFile')->for([V::class, TypeAny::class, Type::class])->callback([self::class, 'asFile'])->return(TypeFile::class),
            Registrar::make('asBool')->for([V::class, TypeAny::class, Type::class])->callback([self::class, 'asBool'])->return(TypeBool::class),
            Registrar::make('asDate')->for([V::class, TypeAny::class, Type::class])->callback([self::class, 'asDate'])->return(TypeStringDate::class),
            Registrar::make('asEmail')->for([V::class, TypeAny::class, Type::class])->callback([self::class, 'asEmail'])->return(TypeStringEmail::class),
            Registrar::make('asScalar')->for([V::class, TypeAny::class, Type::class])->callback([self::class, 'asScalar'])->return(TypeScalar::class),
            Registrar::make('asFloat')->for([V::class, TypeAny::class, Type::class])->callback([self::class, 'asFloat'])->return(TypeFloat::class),
            Registrar::make('asGroup')->for([V::class, TypeAny::class, Type::class])->callback([self::class, 'asGroup'])->return(TypeGroup::class),
            Registrar::make('asInt')->for([V::class, TypeAny::class, Type::class])->callback([self::class, 'asInt'])->return(TypeInt::class),
            Registrar::make('asIntString')->for([V::class, TypeAny::class, Type::class])->callback([self::class, 'asIntString'])->return(TypeStringInt::class),
            Registrar::make('asNumber')->for([V::class, TypeAny::class, Type::class])->callback([self::class, 'asNumber'])->return(TypeNumber::class),
            Registrar::make('asString')->for([V::class, TypeAny::class, Type::class])->callback([self::class, 'asString'])->return(TypeString::class),

            // [[RootType::class], 'asAny', [self::class, 'asAny'], RootType::class], //
            // [V::class, 'and', [self::class, 'and'], TypeOr::class],
            // [V::class, 'intWithClass', [self::class, 'intWithClass'], TypeInt::class],

        ]);
    }

    public static function any()
    {
        return function (?Type $obj) {
            return (new Type())->from($obj);
        };
    }

    public static function email(Options $options = null)
    {
        return function (?Type $obj) use ($options) {
            $options = Options::build($options, func_get_args());
            $type = (new TypeStringEmail())->from($obj);
            $type->addRule(Rules::email($options->getErrorMessage()), $options);

            return $type;
        };
    }

    /**
     * @param  array|callable  $setup
     */
    public static function group($setup = null, bool $stopOnFieldFailure = false, Options $options = null)
    {
        return function (?Type $obj) use ($setup, $stopOnFieldFailure, $options) {
            $options = Options::build($options, func_get_args());
            $type = (new TypeGroup())->from($obj)->init($setup);
            $type->addRule(Rules::array($options->getErrorMessage()), $options);
            $type->addRule($type->getGroupRule($stopOnFieldFailure, $options->getErrorMessage()), $options);

            return $type;
        };
    }

    /**
     * @param  \Kedniko\Vivy\Type[]  $types
     */
    public static function or(array $types, Options $options = null)
    {
        return function (?Type $obj) use ($types, $options) {
            $options = Options::build($options, func_get_args());

            return (new TypeOr())->from($obj)->init($types, false, $options);
        };
    }

    // /**
    //  * @param \Kedniko\Vivy\Type[] $types
    //  * @param Options|null $options
    //  */
    // public static function and(array $types, Options $options = null)
    // {
    // 	$options = Options::build($options, func_get_args());
    // 	$type = new TypeOr($types, false, $options);
    // 	return $type;
    // }

    /**
     * @param  Type[]  $types
     */
    public static function notIn($types, Options $options = null)
    {
        return function (?Type $obj) use ($options, $types) {
            $options = Options::build($options, func_get_args());

            return (new TypeOr($types, true, $options))->from($obj);
        };
    }

    public static function file(Options $options = null)
    {
        return function (?Type $obj) use ($options) {
            $options = Options::build($options, func_get_args());
            $type = (new TypeFile())->from($obj);
            $type->addRule(Rules::file($options->getErrorMessage()), $options);

            return $type;
        };
    }

    public static function int(Options $options = null)
    {
        return function (?Type $obj) use ($options) {
            $options = Options::build($options, func_get_args());
            $type = (new TypeInt())->from($obj);
            $type->addRule(Rules::int($options->getErrorMessage()), $options);

            return $type;
        };
    }

    // public static function intWithClass(Options $options = null)
    // {
    // 	$options = Options::build($options, func_get_args());
    // 	$type = new TypeInt();
    // 	$type->addRule(Rules::intWithClass($options->getErrorMessage()), $options);
    // 	return $type;
    // }

    public static function bool(Options $options = null)
    {
        return function (?Type $obj) use ($options) {
            $options = Options::build($options, func_get_args());
            $type = (new TypeBool())->from($obj);
            $type->addRule(Rules::bool($options->getErrorMessage()), $options);

            return $type;
        };
    }

    public static function float($strictFloat = false, Options $options = null)
    {
        return function (?Type $obj) use ($strictFloat, $options): \Kedniko\Vivy\Plugins\StandardLibrary\TypeFloat {
            $options = Options::build($options, func_get_args());
            $type = new TypeFloat();
            $type->addRule(Rules::float($strictFloat, $options->getErrorMessage()), $options);

            return $type;
        };
    }

    public static function number(Options $options = null)
    {
        return function (?Type $obj) use ($options) {
            $options = Options::build($options, func_get_args());
            $type = (new TypeNumber())->from($obj);
            $type->addRule(Rules::floatOrInt($options->getErrorMessage()), $options);

            return $type;
        };
    }

    public static function numberString(Options $options = null)
    {
        return function (?Type $obj) use ($options) {
            $options = Options::build($options, func_get_args());
            $type = (new TypeStringNumber())->from($obj);
            $type->addRule(Rules::numberString(false, $options->getErrorMessage()), $options);

            return $type;
        };
    }

    public static function string(Options $options = null)
    {
        return function (?Type $obj) use ($options) {
            $options = Options::build($options, func_get_args());
            $type = (new TypeString())->from($obj);
            $type->addRule(Rules::string($options->getErrorMessage()), $options);
            $type->allowEmptyString();

            return $type;
        };
    }

    public static function digits(Options $options = null)
    {
        return function (?Type $obj) use ($options) {
            $options = Options::build($options, func_get_args());
            $type = (new TypeStringInt())->from($obj);
            $type->addRule(Rules::digitsString(false, $options->getErrorMessage()), $options);

            return $type;
        };
    }

    public static function intString(Options $options = null)
    {
        return function (?Type $obj) use ($options) {
            $options = Options::build($options, func_get_args());
            $type = (new TypeStringInt())->from($obj);
            $type->addRule(Rules::intString(false, $options->getErrorMessage()), $options);

            return $type;
        };
    }

    public static function boolString(Options $options = null)
    {
        return function (?Type $obj) use ($options) {
            $options = Options::build($options, func_get_args());
            $type = (new TypeStringBool())->from($obj);
            $type->addRule(Rules::boolString($options->getErrorMessage()), $options);

            return $type;
        };
    }

    public static function floatString($strict = true, $trim = false, Options $options = null)
    {
        return function (?Type $obj) use ($strict, $trim, $options) {
            $options = Options::build($options, func_get_args());
            $type = (new TypeStringInt())->from($obj);
            $type->addRule(Rules::floatString($strict, $trim, $options->getErrorMessage()), $options);

            return $type;
        };
    }

    public static function date($format = 'Y-m-d', Options $options = null)
    {
        return function (?Type $obj) use ($format, $options) {
            $options = Options::build($options, func_get_args());
            $type = (new TypeStringDate())->from($obj);
            (new TypeProxy($type))->setChildStateProperty('_extra.format', $format);
            $type->addRule(Rules::date($format, $options->getErrorMessage()), $options);

            return $type;
        };
    }

    public static function everything(Options $options = null)
    {
        return function (?Type $obj) use ($options) {
            $options = Options::build($options, func_get_args());

            return (new Type())->from($obj);
        };
    }

    public static function notFalsy(Options $options = null)
    {
        return function (?Type $obj) use ($options) {
            $options = Options::build($options, func_get_args());
            $type = (new Type())->from($obj);
            $type->addRule(Rules::notFalsy($options->getErrorMessage() ?: RuleMessage::getErrorMessage('default.notFalsy')), $options);

            return $type;
        };
    }

    // public static function allowEmptyString()
    // {
    //     $type = new TypeUnkown();
    //     // TODO
    //     $type->allowEmptyString();
    //     return $type;
    // }
    // public static function allowNullTODO()
    // {
    //     $type = new TypeUnkown();
    //     $type->allowNull();
    //     return $type;
    // }
    public static function notNull(Options $options = null)
    {
        return function (?Type $obj) use ($options) {
            $options = Options::build($options, func_get_args());

            return (new Type())->from($obj);
        };
    }

    public static function null(Options $options = null)
    {
        return function (?Type $obj) use ($options) {
            $options = Options::build($options, func_get_args());
            $type = (new TypeNull())->from($obj);
            $type->allowNull();
            $type->addRule(Rules::null($options->getErrorMessage() ?: RuleMessage::getErrorMessage('default.'.Rules::ID_NULL)), $options);

            return $type;
        };
    }

    public static function notEmptyString(Options $options = null)
    {
        return function (?Type $obj) use ($options) {
            $options = Options::build($options, func_get_args());
            $type = (new TypeString())->from($obj);
            $type->addRule(Rules::notEmptyString($options->getErrorMessage() ?: RuleMessage::getErrorMessage('default.'.Rules::ID_NOT_EMPTY_STRING)), $options);

            return $type;
        };
    }

    public static function emptyString(Options $options = null)
    {
        return function (?Type $obj) use ($options) {
            $options = Options::build($options, func_get_args());
            $type = (new TypeStringEmpty())->from($obj);
            $type->addRule(Rules::emptyString($options->getErrorMessage() ?: RuleMessage::getErrorMessage('default.'.Rules::ID_EMPTY_STRING)), $options);
            $type->allowEmptyString();

            return $type;
        };
    }

    public static function optional(mixed $default = null)
    {
        return function (?Type $obj) use ($default) {
            $type = (new Type())->from($obj);
            $type->removeRule(Rules::ID_REQUIRED);
            $type->state->setRequired(false);
            if (count(func_get_args())) {
                $type->state->setValueIfOptionalNotExists($default);
            }

            return $type;
        };
    }

    public static function undefined(bool $enableDafault = false, mixed $value = null, Options $options = null)
    {
        return function (?Type $obj) use ($enableDafault, $value, $options) {
            $options = Options::build($options, func_get_args());
            $type = (new TypeUndefined())->from($obj);
            if (static::class === V::class) {
                $type->state->_extra = ['startsWithUndefined' => true];
            }

            if ($enableDafault) {
                $type->_extra = ['default' => $value];
            }
            $type->addRule(Rules::undefined(), $options);

            return $type;
        };
    }

    public static function files(Options $options = null)
    {
        return function (?Type $obj) use ($options) {
            $options = Options::build($options, func_get_args());

            return (new TypeFiles())->from($obj);
        };
    }

    public static function array(Options $options = null)
    {
        return function (?Type $obj) use ($options) {
            $options = Options::build($options, func_get_args());
            $type = (new TypeArray())->from($obj);
            $type->addRule(Rules::array($options->getErrorMessage()), $options);

            return $type;
        };
    }

    public static function make($data, $setup = null, bool $stopOnFieldFailure = false, Options $options = null)
    {
        $type = self::group($setup, $stopOnFieldFailure, $options);
        (new TypeProxy($type))->setData($data);

        return $type;
    }

    // casts

    public static function asFile()
    {
        return function (?Type $obj) {
            return (new TypeFile())->from($obj);
        };
    }

    public static function asArray()
    {
        return function (?Type $obj) {
            return (new TypeArray())->from($obj);
        };
    }

    public static function asBool()
    {
        return function (?Type $obj) {
            return (new TypeBool())->from($obj);
        };
    }

    public static function asAny()
    {
        return function (?Type $obj) {
            return (new TypeAny())->from($obj);
        };
    }

    public static function asDate($format = 'Y-m-d', Options $options = null)
    {
        return function (?Type $obj) use ($format) {
            $type = (new TypeStringDate())->from($obj);
            (new TypeProxy($type))->setChildStateProperty('_extra.format', $format);

            return $type;
        };
    }

    public static function asEmail()
    {
        return function (?Type $obj) {
            return (new TypeStringEmail())->from($obj);
        };
    }

    public static function asScalar()
    {
        return function (?Type $obj) {
            return (new TypeScalar())->from($obj);
        };
    }

    public static function asFloat()
    {
        return function (?Type $obj) {
            return (new TypeFloat())->from($obj);
        };
    }

    public static function asGroup()
    {
        return function (?Type $obj) {
            return (new TypeGroup())->from($obj);
        };
    }

    public static function asInt()
    {
        return function (?Type $obj) {
            return (new TypeInt())->from($obj);
        };
    }

    public static function asIntString()
    {
        return function (?Type $obj) {
            return (new TypeStringInt())->from($obj);
        };
    }

    public static function asNumber()
    {
        return function (?Type $obj) {
            return (new TypeNumber())->from($obj);
        };
    }

    public static function asString()
    {
        return function (?Type $obj) {
            return (new TypeString())->from($obj);
        };
    }
}
