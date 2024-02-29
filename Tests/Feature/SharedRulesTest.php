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
