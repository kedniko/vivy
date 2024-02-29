<?php

namespace Kedniko\Vivy\Messages;

use Kedniko\Vivy\Support\Arr;

class Messages
{

    public static function getErrorMessage(string $errorID = null, string $lang = null)
    {
        if ($lang) {
            Error::setLocale($lang);
        }
        if ($errorID) {
            $err = Error::get($errorID);
            if ($err) {
                return $err;
            }
            $err = Error::get('default.' . $errorID);
            if ($err) {
                return $err;
            }
        }
        $type = explode('.', $errorID)[0];
        $err = Error::get($type . '.default.generic');
        if ($err) {
            return $err;
        }

        return Error::getDefaultError();
    }
}
