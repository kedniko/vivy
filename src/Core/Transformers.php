<?php

namespace Kedniko\Vivy\Core;

use Kedniko\Vivy\Context;
use Kedniko\Vivy\Exceptions\VivyTransformerException;
use Kedniko\Vivy\Messages\TransformerMessage;
use Kedniko\Vivy\Transformer;

class Transformers
{
    const ID_STRING_TO_BOOL = 'stringToBool';

    const ID_STRING_TO_INT = 'stringToInt';

    const ID_BOOL_TO_INT = 'boolToInt';

    const ID_BOOL_TO_STRING = 'boolToString';

    const ID_INT_TO_STRING = 'intToString';

    const ID_INT_TO_BOOL = 'intToBool';

    const ID_TRIM = 'trim';

    const ID_LTRIM = 'ltrim';

    const ID_RTRIM = 'rtrim';

    const ID_TO_UPPER_CASE = 'toUpperCase';

    const ID_TO_LOWER_CASE = 'toLowerCase';

    const ID_FIRST_LETTER_UPPER_CASE = 'firstLetterUpperCase';

    const ID_FIRST_LETTER_LOWER_CASE = 'firstLetterLowerCase';

    const ID_NUMBER_TO_STRING = 'numberToString';

    public static function trim($characters = " \t\n\r\0\x0B", $errormessage = null)
    {
        $transformerID = self::ID_TRIM;
        $errormessage = $errormessage ?: TransformerMessage::getErrorMessage($transformerID);

        return new Transformer($transformerID, function (Context $c) use ($characters) {
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

    public static function ltrim($characters = " \t\n\r\0\x0B", $errormessage = null)
    {
        $transformerID = self::ID_LTRIM;
        $errormessage = $errormessage ?: TransformerMessage::getErrorMessage($transformerID);

        return new Transformer($transformerID, function (Context $c) use ($characters) {
            $value = $c->value;
            if (! is_string($value)) {
                throw new VivyTransformerException();
            }

            return ltrim($value, $characters);
        }, $errormessage);
    }

    public static function rtrim($characters = " \t\n\r\0\x0B", $errormessage = null)
    {
        $transformerID = self::ID_RTRIM;
        $errormessage = $errormessage ?: TransformerMessage::getErrorMessage($transformerID);

        return new Transformer($transformerID, function (Context $c) use ($characters) {
            $value = $c->value;
            if (! is_string($value)) {
                throw new VivyTransformerException();
            }

            return rtrim($value, $characters);
        }, $errormessage);
    }

    public static function toUpperCase($errormessage = null)
    {
        $transformerID = self::ID_TO_UPPER_CASE;
        $errormessage = $errormessage ?: TransformerMessage::getErrorMessage($transformerID);

        return new Transformer($transformerID, function (Context $c) {
            $value = $c->value;
            if (! is_string($value)) {
                throw new VivyTransformerException();
            }

            return strtoupper($value);
        }, $errormessage);
    }

    public static function toLowerCase($errormessage = null)
    {
        $transformerID = self::ID_TO_LOWER_CASE;
        $errormessage = $errormessage ?: TransformerMessage::getErrorMessage($transformerID);

        return new Transformer($transformerID, function (Context $c) {
            $value = $c->value;
            if (! is_string($value)) {
                throw new VivyTransformerException();
            }

            return mb_strtolower($value, 'UTF-8');
        }, $errormessage);
    }

    public static function firstLetterUpperCase($errormessage = null)
    {
        $transformerID = self::ID_FIRST_LETTER_UPPER_CASE;
        $errormessage = $errormessage ?: TransformerMessage::getErrorMessage($transformerID);

        return new Transformer($transformerID, function (Context $c) {
            $value = $c->value;
            if (! is_string($value)) {
                throw new VivyTransformerException();
            }

            return ucfirst($value);
        }, $errormessage);
    }

    public static function firstLetterLowerCase($errormessage = null)
    {
        $transformerID = self::ID_FIRST_LETTER_LOWER_CASE;
        $errormessage = $errormessage ?: TransformerMessage::getErrorMessage($transformerID);

        return new Transformer($transformerID, function (Context $c) {
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
     * @return Transformer
     *
     * @throws VivyTransformerException
     */
    public static function stringToInt($errormessage = null)
    {
        $transformerID = self::ID_STRING_TO_INT;
        $errormessage = $errormessage ?: TransformerMessage::getErrorMessage($transformerID);

        return new Transformer($transformerID, function (Context $c) {
            $value = $c->value;

            if (! is_string($value)) {
                throw new VivyTransformerException('This is not a string');
            }

            $isTypeIntString = preg_match(Constants::REGEX_INTEGER_POSITIVE_OR_NEGATIVE, $value) === 1;
            if (! $isTypeIntString) {
                throw new VivyTransformerException('String does not contain an integer');
            }

            return intval($value);
        }, $errormessage);
    }

    public static function stringBoolToBool($errormessage = null)
    {
        $transformerID = self::ID_STRING_TO_BOOL;
        $errormessage = $errormessage ?: TransformerMessage::getErrorMessage($transformerID);

        return new Transformer($transformerID, function (Context $c) {
            $value = $c->value;

            if (! is_string($value)) {
                throw new VivyTransformerException(json_encode($value).' is not a string');
            }

            if (! in_array($c->value, ['true', 'false'], true)) {
                throw new VivyTransformerException($value.' is not allowed in strict mode');
            }

            return $value === 'true';
        }, $errormessage);
    }

    public static function intToString($errormessage = null)
    {
        $transformerID = self::ID_INT_TO_STRING;
        $errormessage = $errormessage ?: TransformerMessage::getErrorMessage($transformerID);

        return new Transformer($transformerID, function (Context $c) {
            $value = $c->value;

            if (! is_int($value)) {
                throw new VivyTransformerException();
            }

            try {
                return strval($value);
            } catch (\Exception $th) {
                throw new VivyTransformerException();
            }
        }, $errormessage);
    }

    public static function numberToString($errormessage = null)
    {
        $transformerID = self::ID_NUMBER_TO_STRING;
        $errormessage = $errormessage ?: TransformerMessage::getErrorMessage($transformerID);

        return new Transformer($transformerID, function (Context $c) {
            $value = $c->value;

            if (! is_int($value) && ! is_float($value)) {
                throw new VivyTransformerException();
            }

            try {
                return strval($value);
            } catch (\Exception $th) {
                throw new VivyTransformerException();
            }
        }, $errormessage);
    }

    public static function boolToInt($errormessage = null)
    {
        $transformerID = self::ID_BOOL_TO_INT;
        $errormessage = $errormessage ?: TransformerMessage::getErrorMessage($transformerID);

        return new Transformer($transformerID, function (Context $c) {
            $value = $c->value;

            if (! is_bool($value)) {
                throw new VivyTransformerException('This is not a bool');
            }

            return $value === true ? 1 : 0;
        }, $errormessage);
    }

    public static function boolToString($errormessage = null)
    {
        $transformerID = self::ID_BOOL_TO_STRING;
        $errormessage = $errormessage ?: TransformerMessage::getErrorMessage($transformerID);

        return new Transformer($transformerID, function (Context $c) {
            $value = $c->value;

            if (! is_bool($value)) {
                throw new VivyTransformerException('This is not a bool');
            }

            return $value === true ? 'true' : 'false';
        }, $errormessage);
    }
}
