<?php

namespace Tests;

use App\Rules;
use App\Token;
use Kedniko\Vivy\O;
use Kedniko\Vivy\V;
use App\Types\TypeToken;
use Kedniko\Vivy\Contracts\TypeInterface;
use Kedniko\Vivy\Core\Validated;
use Kedniko\Vivy\Messages\Error;
use Kedniko\Vivy\Serializer;

uses()->group('serializer');


test('serializer-obj-to-json', function () {

    $v = V::email()->minLength(3)->maxLength(5)->date()->between('2020-01-01', '2020-12-31');
    $res = (new Serializer)->encode($v);
    $expectedSerializedResult = [
        'rules' => [
            'email' => [],
            'minLength' => [3],
            'maxLength' => [5],
            'date' => ['Y-m-d'],
            'between' => ['2020-01-01', '2020-12-31'],
        ],
    ];

    expect($expectedSerializedResult)->toBe($res);
});

test('serializer-json-to-obj', function () {
    $json = [
        'rules' => [
            'email' => [],
            'minLength' => [3],
            'maxLength' => [20],
        ],
    ];
    $v = (new Serializer)->decode($json);
    $validated = $v->validate('foo@example.com');

    expect($validated->isValid())->toBeTrue();
});

test('serializer-encode-decode', function () {
    $data = 'foo@example.com';
    $v = V::email()->minLength(3)->maxLength(20)->setValue('2024-01-01')->asDate()->date()->between('2020-01-01', '2024-12-31');

    $validated = $v->validate($data);
    expect($validated->isValid())->toBeTrue();
    $json = (new Serializer)->encode($v);

    $v = (new Serializer)->decode($json);
    $validated = $v->validate($data);
    expect($validated->isValid())->toBeTrue();
})->only();
