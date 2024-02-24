<?php

namespace Tests;

use Kedniko\Vivy\V;

uses()->group('beta');

test('failhandlers-1', function () {

    $globalWorks = false;
    $localWorks = false;
    $forceWorks = false;

    V::setGlobalFailHandler('global', function () use (&$globalWorks) {
        $globalWorks = true;
    });

    $v = V::new()
        ->setFailHandler('local', function () use (&$localWorks) {
            $localWorks = true;
        })->group([
            'name' => V::string()->minLength(40),
        ]);

    $validated1 = $v->validate(['name' => 'kedniko',])->orFailWith('global');
    $validated2 = $v->validate(['name' => 'kedniko',])->orFailWith('local');
    V::string()->validate('a')->forceFailWith(function () use (&$forceWorks) {
        $forceWorks = true;
    });

    expect($validated1->isValid())->toBeFalse();
    expect($validated2->isValid())->toBeFalse();
    expect($globalWorks)->toBeTrue();
    expect($localWorks)->toBeTrue();
    expect($forceWorks)->toBeTrue();
});
