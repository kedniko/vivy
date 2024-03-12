<?php

namespace Kedniko\Vivy\Messages;

final class RuleMessage extends Messages
{
    public static function getErrorMessage(?string $errorID = null, ?string $lang = null)
    {
        return parent::getErrorMessage("rules.$errorID", $lang);
    }
}
