<?php

namespace Tests;

use Kedniko\Vivy\V;

uses()->group('shared-rules-test');

test('test-1', function () {

    $sharedRule = V::string();

    $validated = V::group([
        'username' => $sharedRule,
        'password' => $sharedRule,
    ])->validate([
        'username' => 'kedniko',
        'password' => 12345,
    ]);
    expect($validated->isValid())->toBeFalse();

    $validated = V::group([
        'username' => V::string(),
        'password' => V::string(),
    ])->validate([
        'username' => 'kedniko',
        'password' => 12345,
    ]);

    expect($validated->isValid())->toBeFalse();
});


test('group-5', function () {

    $sharedRule = V::int()->in([1, 2, 3, 4]);

    $v = V::group([
        'var_a' => $sharedRule,
        'var_b' => V::or([
            V::undefined()->setValue(null),
            $sharedRule,
        ]),
    ]);

    $validated = V::group($v)->validate([
        'var_a' => 1,
        'var_b' => 3,
    ]);

    expect($validated->isValid())->toBeTrue();
});
