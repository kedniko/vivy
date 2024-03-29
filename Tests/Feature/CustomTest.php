<?php

namespace Tests;

use App\Rules;
use App\Token;
use App\Types\TypeToken;
use Kedniko\Vivy\Messages\Error;
use Kedniko\Vivy\O;
use Kedniko\Vivy\Support\Util;
use Kedniko\Vivy\V;
use Kedniko\VivyPluginStandard\StandardLibrary;

uses()->group('custom');

beforeAll(function () {
    Error::setLocale('it');
    V::registerPlugin(new TypeToken());
    V::registerPlugin(new Rules());
    V::registerPlugin(new StandardLibrary());
    Error::addPath(Util::basePath('src/lang'));
});

test('custom-rule-1', function () {
    $token = new Token();
    $token->expired = false;
    $v = V::token()->notExpired();
    $validated = $v->validate($token);
    expect($validated->isValid())->toBe(true);
});

test('custom-rule-2', function () {
    $v = V::phone()->toPL()->toPL()->toPL()->toUS()->minLength(2);
    $validated = $v->validate('1234');
    expect($validated->isValid())->toBe(true);
    expect($validated->value())->tobe('+1 +48 +48 +48 1234');
});

test('custom-rule-3', function () {
    $v = V::phone()->toIT()->startsWith123(O::message('Non comincia con 123')->continueOnFailure())->toPL(O::message('ops'));
    $validated = $v->validate('0234567890');
    expect($validated->isValid())->toBeFalse();
    expect($validated->errors())->tobe(['startsWith123' => ['Non comincia con 123']]);
    expect($validated->value())->tobe('+48 +39 0234567890');
});
