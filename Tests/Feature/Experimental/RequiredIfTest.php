<?php

namespace Tests;

use Kedniko\Vivy\Contracts\ContextInterface;
use Kedniko\Vivy\V;

uses()->group('required-if');


test('required-if-simple-1', function () {
    $v = V::group([
        'allow_sms' => V::bool(),
        'phone' => V::requiredIf(true),
    ]);
    $validated = $v->validate(['allow_sms' => false]);
    expect($validated->isValid())->toBeFalse();

    $validated = $v->validate(['allow_sms' => true]);
    expect($validated->isValid())->toBeFalse();
});

test('required-if-simple-2', function () {
    $v = V::group([
        'allow_sms' => V::bool(),
        'phone' => V::requiredIf(false)->string(),
    ]);
    $validated = $v->validate(['allow_sms' => false]);
    expect($validated->isValid())->toBeTrue();
});

test('required-if-2', function () {
    $v = V::group([
        'allow_sms' => V::bool()->required(),
        'phone' => V::requiredIf(function (ContextInterface $c) {
            $allowSmsContext = $c->fatherContext->getFieldContext('allow_sms');
            $res = $allowSmsContext->value === true;

            return $res;
        }),
    ]);
    $validated = $v->validate(['allow_sms' => false]);
    expect($validated->isValid())->toBeTrue();

    $validated = $v->validate(['allow_sms' => true]);
    expect($validated->isValid())->toBeFalse();
});

test('required-if-3', function () {
    $v = V::group([
        'allow_sms' => V::bool()->required(),
        'phone' => V::requiredIfField('allow_sms', function (ContextInterface $c) {
            $res = $c->value === true;

            return $res;
        }),
    ]);
    $validated = $v->validate(['allow_sms' => false]);
    expect($validated->isValid())->toBeTrue();

    $validated = $v->validate(['allow_sms' => true]);
    expect($validated->isValid())->toBeFalse();
});
