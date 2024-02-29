<?php

namespace Tests;

use Kedniko\Vivy\V;
use Kedniko\Vivy\Core\GroupContext;

uses()->group('dynamic');

test('dynamic-1', function () {
    // $v = V::group(function ($c) {
    //     return [
    //         'address' => [
    //             'via' => V::string(),
    //             'citta' => V::string()->toUppercase(),
    //             'stato' => V::string()->maxLength(2),
    //         ],
    //         'status' => V::null()->onValid(function (GroupContext $c) {
    //             $c->appendField('count', V::int());
    //             $c->appendFieldAfterCurrent('numbers', V::array()->count(10)->minCount(9)->maxCount(11)->each(V::int()));
    //         }),
    //     ];
    // });
    $v = V::group([
        'address' => [
            'via' => V::string(),
            'citta' => V::string()->toUppercase(),
            'stato' => V::string()->maxLength(2),
        ],
        'status' => V::null(),
        // ->onValid(function (GroupContext $c) {
        // 	$c->appendField('count', V::int());
        // 	$c->appendFieldAfterCurrent('numbers', V::array()->count(10)->minCount(9)->maxCount(11)->each(V::int()));
        // }),
    ]);

    $validated = $v->validate([
        'name' => 'niko',
        'count' => 340,
        'address' => [
            'via' => 'main street',
            'citta' => 'new York',
            'stato' => 'PL',
        ],
        'numbers' => [1, 2, 3, 4, 5, 6, 7, 8, 9, 10],
        'status' => null,
    ]);

    $expected = [
        'name' => 'niko',
        'count' => 340,
        'address' => [
            'via' => 'main street',
            'citta' => 'NEW YORK',
            'stato' => 'PL',
        ],
        'numbers' => [1, 2, 3, 4, 5, 6, 7, 8, 9, 10],
        'status' => null,
    ];

    $isvalid = $validated->isValid();

    expect($validated->value())->tobe($expected, 'Validated post non è expected');
    expect($isvalid)->toBeTrue();
});
