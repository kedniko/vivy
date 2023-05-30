<?php

namespace Kedniko\Vivy\Messages;

use Kedniko\Vivy\Support\Arr;

class Messages
{
    const LANG_IT = 'it';

    const LANG_EN = 'en';

    // private static $enableLangFallback = true;
    protected static $langFallback = self::LANG_IT;

    private const ENABLE_DEFAULT_ERROR_FALLBACK = true;

    protected static $messages;

    protected static $defaultMessage = 'Errore';

    private static function getAllLanguages()
    {
        return [
            self::LANG_IT,
            self::LANG_EN,
        ];
    }

    /* abstract */
    protected static function getMessagesArray($lang)
    {
        // Limitazione di php 5.5.9 e precedenti fino a versione ?.?.? di php
        // Strict Standards: Static function Class::foo() should not be abstract

        return [];
    }

    /**
     * @param  string|null  $errorID
     * @param  mixed|null  $givenValue
     * @param  bool  $showGivenValue
     */
    public static function getErrorMessage($errorID = null, $lang = Messages::LANG_IT, $givenValue = null, $showGivenValue = false)
    {
        if (! in_array($lang, self::getAllLanguages(), true)) {
            $lang = self::$langFallback;
        }

        // error
        $message = self::getMessage($errorID, $lang) ?:
            // default error by type
            (self::ENABLE_DEFAULT_ERROR_FALLBACK ? self::getMessage("default.{$errorID}", $lang) : null) ?:
            // default error
            (self::ENABLE_DEFAULT_ERROR_FALLBACK ? self::getMessage('default.generic', $lang) : null) ?:
            // fallback language
            // (self::$enableLangFallback ?  self::getMessage($errorID, self::$langFallback) : null) ?:
            // default error
            static::$defaultMessage;

        if ($showGivenValue) {
            if (is_array($givenValue)) {
                $givenValue = json_encode($givenValue);
            }

            $message = $message.'. '.self::getErrorMessage('default.riceived', $lang).': ['.gettype($givenValue).']'.$givenValue;
        }

        return $message;
    }

    private static function getMessage($errorID, $lang)
    {
        if (! $errorID) {
            return null;
        }

        $messagesArr = static::getMessagesArray($lang);

        $value = Arr::get($messagesArr, $errorID);

        return is_string($value) ? $value : null;
    }
}
