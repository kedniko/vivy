<?php

namespace Kedniko\Vivy\Core;

use Kedniko\Vivy\Context;
use Kedniko\Vivy\Exceptions\VivyTransformerException;
use Kedniko\Vivy\Messages\TransformerMessage;
use Kedniko\Vivy\Transformer;

final class Transformers
{
    public const ID_STRING_TO_BOOL = 'stringToBool';

    public const ID_STRING_TO_INT = 'stringToInt';

    public const ID_BOOL_TO_INT = 'boolToInt';

    public const ID_BOOL_TO_STRING = 'boolToString';

    public const ID_INT_TO_STRING = 'intToString';

    public const ID_INT_TO_BOOL = 'intToBool';

    public const ID_TRIM = 'trim';

    public const ID_LTRIM = 'ltrim';

    public const ID_RTRIM = 'rtrim';

    public const ID_TO_UPPER_CASE = 'toUpperCase';

    public const ID_TO_LOWER_CASE = 'toLowerCase';

    public const ID_FIRST_LETTER_UPPER_CASE = 'firstLetterUpperCase';

    public const ID_FIRST_LETTER_LOWER_CASE = 'firstLetterLowerCase';

    public const ID_NUMBER_TO_STRING = 'numberToString';

    public static function trim($characters = " \t\n\r\0\x0B", $errormessage = null): Transformer
    {
        $transformerID = self::ID_TRIM;
        $errormessage = $errormessage ?: TransformerMessage::getErrorMessage($transformerID);

        return new Transformer($transformerID, function (Context $c) use ($characters): ?string {
            $value = $c->value;
            if ($value === null) {
                return $value;
            }
            if (! is_string($value)) {
                throw new VivyTransformerException();
            }

            return trim($value, $characters);
        }, $errormessage);
    }

    public static function ltrim($characters = " \t\n\r\0\x0B", $errormessage = null): Transformer
    {
        $transformerID = self::ID_LTRIM;
        $errormessage = $errormessage ?: TransformerMessage::getErrorMessage($transformerID);

        return new Transformer($transformerID, function (Context $c) use ($characters): string {
            $value = $c->value;
            if (! is_string($value)) {
                throw new VivyTransformerException();
            }

            return ltrim($value, $characters);
        }, $errormessage);
    }

    public static function rtrim($characters = " \t\n\r\0\x0B", $errormessage = null): Transformer
    {
        $transformerID = self::ID_RTRIM;
        $errormessage = $errormessage ?: TransformerMessage::getErrorMessage($transformerID);

        return new Transformer($transformerID, function (Context $c) use ($characters): string {
            $value = $c->value;
            if (! is_string($value)) {
                throw new VivyTransformerException();
            }

            return rtrim($value, $characters);
        }, $errormessage);
    }

    public static function toUpperCase($errormessage = null): Transformer
    {
        $transformerID = self::ID_TO_UPPER_CASE;
        $errormessage = $errormessage ?: TransformerMessage::getErrorMessage($transformerID);

        return new Transformer($transformerID, function (Context $c): string {
            $value = $c->value;
            if (! is_string($value)) {
                throw new VivyTransformerException();
            }

            return strtoupper($value);
        }, $errormessage);
    }

    public static function toLowerCase($errormessage = null): Transformer
    {
        $transformerID = self::ID_TO_LOWER_CASE;
        $errormessage = $errormessage ?: TransformerMessage::getErrorMessage($transformerID);

        return new Transformer($transformerID, function (Context $c): string {
            $value = $c->value;
            if (! is_string($value)) {
                throw new VivyTransformerException();
            }

            return mb_strtolower($value, 'UTF-8');
        }, $errormessage);
    }

    public static function firstLetterUpperCase($errormessage = null): Transformer
    {
        $transformerID = self::ID_FIRST_LETTER_UPPER_CASE;
        $errormessage = $errormessage ?: TransformerMessage::getErrorMessage($transformerID);

        return new Transformer($transformerID, function (Context $c): string {
            $value = $c->value;
            if (! is_string($value)) {
                throw new VivyTransformerException();
            }

            return ucfirst($value);
        }, $errormessage);
    }

    public static function firstLetterLowerCase($errormessage = null): Transformer
    {
        $transformerID = self::ID_FIRST_LETTER_LOWER_CASE;
        $errormessage = $errormessage ?: TransformerMessage::getErrorMessage($transformerID);

        return new Transformer($transformerID, function (Context $c): string {
            $value = $c->value;
            if (! is_string($value)) {
                throw new VivyTransformerException();
            }

            return lcfirst($value);
        }, $errormessage);
    }

    /**
     * @todo support integers bigger than "2147483647" https://www.php.net/manual/en/function.intval.php
     *
     * @param  null  $errormessage
     *
     * @throws VivyTransformerException
     */
    public static function stringToInt($errormessage = null): Transformer
    {
        $transformerID = self::ID_STRING_TO_INT;
        $errormessage = $errormessage ?: TransformerMessage::getErrorMessage($transformerID);

        return new Transformer($transformerID, function (Context $c): int {
            $value = $c->value;

            if (! is_string($value)) {
                throw new VivyTransformerException('This is not a string');
            }

            $isTypeIntString = preg_match(Constants::REGEX_INTEGER_POSITIVE_OR_NEGATIVE, $value) === 1;
            if (! $isTypeIntString) {
                throw new VivyTransformerException('String does not contain an integer');
            }

            return (int) $value;
        }, $errormessage);
    }

    public static function stringBoolToBool($errormessage = null): Transformer
    {
        $transformerID = self::ID_STRING_TO_BOOL;
        $errormessage = $errormessage ?: TransformerMessage::getErrorMessage($transformerID);

        return new Transformer($transformerID, function (Context $c): bool {
            $value = $c->value;

            if (! is_string($value)) {
                throw new VivyTransformerException(json_encode($value, JSON_THROW_ON_ERROR).' is not a string');
            }

            if (! in_array($c->value, ['true', 'false'], true)) {
                throw new VivyTransformerException($value.' is not allowed in strict mode');
            }

            return $value === 'true';
        }, $errormessage);
    }

    public static function intToString($errormessage = null): Transformer
    {
        $transformerID = self::ID_INT_TO_STRING;
        $errormessage = $errormessage ?: TransformerMessage::getErrorMessage($transformerID);

        return new Transformer($transformerID, function (Context $c) {
            $value = $c->value;

            if (! is_int($value)) {
                throw new VivyTransformerException();
            }

            try {
                return (string) $value;
            } catch (\Exception) {
                throw new VivyTransformerException();
            }
        }, $errormessage);
    }

    public static function numberToString($errormessage = null): Transformer
    {
        $transformerID = self::ID_NUMBER_TO_STRING;
        $errormessage = $errormessage ?: TransformerMessage::getErrorMessage($transformerID);

        return new Transformer($transformerID, function (Context $c) {
            $value = $c->value;

            if (! is_int($value) && ! is_float($value)) {
                throw new VivyTransformerException();
            }

            try {
                return (string) $value;
            } catch (\Exception) {
                throw new VivyTransformerException();
            }
        }, $errormessage);
    }

    public static function boolToInt($errormessage = null): Transformer
    {
        $transformerID = self::ID_BOOL_TO_INT;
        $errormessage = $errormessage ?: TransformerMessage::getErrorMessage($transformerID);

        return new Transformer($transformerID, function (Context $c): int {
            $value = $c->value;

            if (! is_bool($value)) {
                throw new VivyTransformerException('This is not a bool');
            }

            return $value ? 1 : 0;
        }, $errormessage);
    }

    public static function boolToString($errormessage = null): Transformer
    {
        $transformerID = self::ID_BOOL_TO_STRING;
        $errormessage = $errormessage ?: TransformerMessage::getErrorMessage($transformerID);

        return new Transformer($transformerID, function (Context $c): string {
            $value = $c->value;

            if (! is_bool($value)) {
                throw new VivyTransformerException('This is not a bool');
            }

            return $value ? 'true' : 'false';
        }, $errormessage);
    }
}
