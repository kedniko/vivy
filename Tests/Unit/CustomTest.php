<?php

namespace Tests;

use App\App;
use Kedniko\Vivy\O;
use Kedniko\Vivy\V;

uses()->group('custom');

beforeAll(function () {
    App::boot();
});

test('custom-rule-1', function () {
    $token = new \App\Token();
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
