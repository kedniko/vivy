<?php

namespace Tests;

use Kedniko\Vivy\O;
use Kedniko\Vivy\V;
use Kedniko\Vivy\Contracts\ContextInterface;

uses()->group('context-test');

test('context-get-middleware-id', function () {

    $itWorks = false;

    V::group([
        'username' => V::string(O::message(function (ContextInterface $c) use (&$itWorks) {
            $id = $c->getMiddleware()->getID();
            if ($id === 'string') {
                $itWorks = true;
            }
        })),
    ])->validate([
        'username' => null,
    ]);

    expect($itWorks)->toBeTrue();
})->only();
