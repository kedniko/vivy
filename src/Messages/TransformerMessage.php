<?php

namespace Kedniko\Vivy\Messages;

final class TransformerMessage extends Messages
{
    public static function getErrorMessage(?string $errorID = null, ?string $lang = null)
    {
        return parent::getErrorMessage("transformers.$errorID", $lang);
    }
}
