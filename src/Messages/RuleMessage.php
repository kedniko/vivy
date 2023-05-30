<?php

namespace Kedniko\Vivy\Messages;

final class RuleMessage extends Messages
{
    protected static $defaultMessage = 'Validazione fallita';

    protected static function getMessagesArray($lang)
    {
        $key = 'rules';
        [];

        $messagesLang = isset(self::$messages[$lang][$key]);

        // read and cache
        if (! $messagesLang) {
            $messages = [];
            $filename = __DIR__."/../lang/{$lang}/{$key}.php";

            if (file_exists($filename)) {
                $messages = require $filename;
            } else {
                $langFallback = self::$langFallback;
                $filenameFallback = __DIR__."/../lang/{$langFallback}/{$key}.php";

                if (file_exists($filenameFallback)) {
                    $messages = require $filenameFallback;
                }
            }

            self::$messages[$lang][$key] = $messages ?: [];
        }

        return self::$messages[$lang][$key];
    }
}
