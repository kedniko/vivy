<?php

namespace Tests;

use Kedniko\Vivy\O;
use Kedniko\Vivy\V;
use Kedniko\Vivy\Messages\Error;

uses()->group('group');

beforeEach(function () {
    Error::setLocale('it');
});

test('group-0', function () {
    $v = V::group([
        'nome' => V::string()->minLength(4)->maxLength(10),
        'cognome' => V::string()->length(5),
        'age' => V::int()->min(18)->max(99),
    ]);
    $validated = $v->validate([
        'nome' => '1234567890',
        'cognome' => '12345',
        'age' => 99,
    ]);
    expect($validated->isValid())->toBeTrue();
});

test('group-1', function () {
    $validated = V::group([
        'name' => V::string(O::message('name is not a string')),
        'count' => V::int()->max(100, O::message('count is too big')),
        'address' => [
            'address' => V::string(),
            'city' => V::string(),
            'country' => V::string()->length(3, O::message('Length invalid')),
        ],
    ])->validate([
        'name' => 3,
        'count' => 340,
        'address' => [
            'address' => 'main street',
            'city' => 'new York',
            'country' => 'PL',
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
            'country' => [
                'length' => ['Length invalid'],
            ],
        ],
    ];

    expect($validated->errors())->toBe($errorsExpected);
});

test('group-2', function () {
    $emailSchema = [
        'name' => V::string(O::message('Name invalid')),
        'email_address' => V::email(O::message('Email invalid')),
        'newsletter_active' => V::bool(), // messaggio di errore di default
    ];

    $schema = V::group([
        'name' => V::string(O::message('Name invalid')),
        'surname' => V::string(O::message('Surname invalid')),
        'emails' => V::array()->each($emailSchema),
    ]);

    $validated = $schema->validate([
        'name' => 'John',
        'surname' => 'Doe',
        'emails' => [
            [
                'name' => 'John 1',
                'email_address' => 'john1_wrong_email',
                'newsletter_active' => true,
            ],
            [
                'name' => 'John 2',
                'email_address' => 'john2@example.com',
                'newsletter_active' => false,
            ],
        ],
    ]);

    $is_valid = $validated->isValid(); // true
    $errors = $validated->errors();
    $expectedErrors = [
        'emails' => [
            0 => [
                'email_address' => [
                    'email' => [
                        'Email invalid',
                    ],
                ],
            ],
        ],
    ];

    expect($is_valid)->toBeFalse();
    expect($errors)->toBe($expectedErrors);
});

test('group-3', function () {
    $validated = V::group([
        'sale_group' => V::string(O::continueOnFailure())->asAny()->int(),
        'sale_id' => V::string(),
        'exclude_taxes' => V::bool(),
        'dates' => V::array()->maxCount(5)->each(V::date('Y-m-d')),
        'rows' => V::array()->each([
            'discount' => V::float(),
            'duration' => V::int(),
            'operator_id' => V::string(),
            'price_single' => V::number(),
            'product_id' => V::string(),
            'quantity' => V::int(),
        ]),
    ])->validate([
        'sale_group' => 21.1,
        'id' => null,
        'exclude_taxes' => true,
        'sale_dates' => [],
        'rows' => [
            [
                'discount' => 21.21,
                'duration' => 0,
                'operator_id' => '4386578437465',
                'price_single' => 20.00,
                'product_id' => '754389758',
                'quantity' => 4,
            ],
        ],
    ]);

    $expectedErrors = [
        'sale_group' => [
            'string' => ['Validazione fallita'],
            'int' => ['Validazione fallita'],
        ],
        'sale_id' => [
            'required' => ['Questo campo è obbligatorio'],
        ],
        'dates' => [
            'required' => ['Questo campo è obbligatorio'],
        ],
    ];

    expect($validated->isValid())->toBeFalse();
    $err = $validated->errors();
    expect($err)->toBe($expectedErrors);
});


test('group-4', function () {
    $v = V::group([
        'person' => V::group([
            'name' => V::string(),
        ])
    ]);

    $validated = V::group($v)->validate([
        'person' => [
            'name' => 'kedniko',
        ],
    ]);

    expect($validated->isValid())->toBeTrue();
});


test('group-with-is', function () {

    $v = V::group([
        "usage" => V::is('usato'),
        "origin" => V::is('nazionale'),
        "registration_date" => V::date('m/Y'),
        "km" => V::is('11000'),
    ]);

    $validated = $v->validate([
        "usage" => "usato",
        "origin" => "nazionale",
        "registration_date" => "07/2018",
        "km" => "11000",
    ]);

    expect($validated->isValid())->toBeTrue();
});
