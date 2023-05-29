<?php

namespace Tests;

use App\App;
use Kedniko\Vivy\O;
use Kedniko\Vivy\V;

uses()->group('group');

beforeAll(function () {
    App::boot();
});

// test('optional', function () {
// 	$v = V::group([
// 		'name'    => V::optional()->setValue('default name'),
// 		'cognome' => V::optional('niko')->string()->setValue('default name'),
// 		'count'   => V::int()->setValue(1000),
// 	]);

// 	$validated = $v->validate([
// 		'name'  => 3,
// 		'count' => 340,
// 	]);

// 	expect($validated->isValid())->toBeTrue();
// 	expect($validated->value())->toBe([
// 		'name'  => 'default name',
// 		'count' => 1000,
// 	]);
// });

test('form-1', function () {
    $validated = V::group([
        'name' => V::string(true, true, true, O::message('name is not a string')),
        'count' => V::int()->max(100, O::message('count is too big')),
        'address' => [
            'via' => V::string(),
            'citta' => V::string(),
            'stato' => V::string()->length(3, O::message('Lunghezza non valida')),
        ],
    ])->validate([
        'name' => 3,
        'count' => 340,
        'address' => [
            'via' => 'main street',
            'citta' => 'new York',
            'stato' => 'PL',
        ],
    ]);

    expect($validated->isValid())->toBeFalse();

    $errorsExpected = [
        'name' => [
            'string' => ['name is not a string'],
        ],
        'count' => [
            'max' => ['count is too big'],
        ],
        'address' => [
            'stato' => [
                'length' => ['Lunghezza non valida'],
            ],
        ],
    ];

    expect($validated->errors())->toBe($errorsExpected);
});

test('example', function () {
    $emailSchema = [
        'nome' => V::string(O::message('Nominativo email non valido')),
        'indirizzo_email' => V::email(O::message('Indirizzo email non valido')),
        'newsletter_active' => V::bool(), // messaggio di errore di default
    ];

    $schema = V::group([
        'nome' => V::string(O::message('Nome non valido')),
        'cognome' => V::string(O::message('Cognome non valido')),
        'emails' => V::array()->each($emailSchema),
    ]);

    $validated = $schema->validate([
        'nome' => 'Nikodem',
        'cognome' => 'Kedzierski',
        'emails' => [
            [
                'nome' => 'Nikodem 1',
                'indirizzo_email' => 'niko1_wrong_email',
                'newsletter_active' => true,
            ],
            [
                'nome' => 'Nikodem 2',
                'indirizzo_email' => 'niko2@example.com',
                'newsletter_active' => false,
            ],
        ],
    ]);

    $is_valid = $validated->isValid(); // true
    $errors = $validated->errors();
    $expectedErrors = [
        'emails' => [
            0 => [
                'indirizzo_email' => [
                    'email' => [
                        'Indirizzo email non valido',
                    ],
                ],
            ],
        ],
    ];

    expect($is_valid)->toBeFalse();
    expect($errors)->toBe($expectedErrors);
});
