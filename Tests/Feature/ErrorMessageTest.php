<?php

namespace Tests;

use Kedniko\Vivy\Support\Arr;
use Kedniko\Vivy\Support\Util;
use Kedniko\Vivy\Messages\Error;

uses()->group('error-message');

test('messages', function () {
    $key = 'string.type';

    foreach (['it', 'en', 'pl'] as $locale) {
        Error::setLocale($locale);
        $err = Error::get("rules.{$key}");
        $a = Arr::get(
            Util::fileContent('src/lang/' . Error::getLocale() . '/rules.php') ?: [],
            $key
        );
        expect($err)->toBe($a);
    }
});
