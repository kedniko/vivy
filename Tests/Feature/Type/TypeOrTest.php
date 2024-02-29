<?php

namespace Tests;

use DateTime;
use Kedniko\Vivy\O;
use Kedniko\Vivy\V;
use Kedniko\Vivy\Core\OrContext;
use Kedniko\Vivy\Messages\Error;
use Kedniko\Vivy\Contracts\ContextInterface;

uses()->group('or');

beforeEach(function () {
    Error::setLocale('it');
});

test('dates', function () {
    $v = V::group([
        'birthday1' => V::string()->date()->minDate(DateTime::createFromFormat('Y-m-d', '1900-01-01'))->maxDate('2022-09-19'),
        'birthday2' => V::date()->between('2010-01-01', '2023-12-31')->toFormat('Y-m-d'),
        'birthday3' => V::or([
            V::int(),
            V::null(),
            V::emptyString(),
            V::date()->equals('2000-01-01')->setValue('3000-01-01'),
            V::undefined()->setValue('3000-01-02'),
        ]),
    ]);
    $validated = $v->validate([
        'birthday1' => '2022-09-19',
        'birthday2' => '2023-01-01',
        // 'birthday3_' => '2000-01-01',
    ]);
    $expValue = [
        'birthday1' => '2022-09-19',
        'birthday2' => '2023-01-01',
        'birthday3' => '3000-01-02',
    ];

    $this->assertTrue($validated->isValid(), 'Non è valido');
    expect($expValue)->toBe($validated->value());
});

test('or-simple', function () {
    $validated = V::group([
        'var' => V::or([
            V::string()->setValue([1, 2])->asArray()->count(3),
            V::string()->setValue('12345')->asString()->length(5)->setValue('yes!'),
        ]),
    ])->validate(['var' => 'aaa']);

    $expValue = ['var' => 'yes!'];

    expect($validated->isValid())->toBeTrue();
    expect($expValue)->toBe($validated->value());
});

test('or', function () {
    $v = V::group([
        'var' => V::or([
            // V::string()->setValue([1, 2])->asArray()->count(3),
            V::string()->setValue('12345')->asString()->length(5)->setValue('yes!'),
        ]),
        'age' => V::or([
            V::string()->setValue('string')->asAny(),
            V::int()->or([
                V::any()->asInt()->min(1000)->setValue('min 100!'),
                V::any()->asInt()->max(500)->setValue('max 500!'),
            ]),
        ]),
        'name' => V::undefined()->setValue('my default value'),
    ]);

    $validated = $v->validate([
        'var' => 'aaa',
        'age' => 20,
    ]);
    $expValue = [
        'var' => 'yes!',
        'age' => 'max 500!',
        'name' => 'my default value',
    ];

    expect($validated->isValid())->toBeTrue();
    expect($validated->value())->toBe($expValue);
});

test('type-or-basic', function () {


    $validated = V::or([
        V::date('Y-m-d H:i:s', O::continueOnFailure())->setValue(1)->asInt()->min(10),
        V::date('Y-m-d'),
        V::date('Y-m'),
        V::null()->setValue('yes!')->asString()->startsWith('a'),
    ], O::message(function (ContextInterface $c) {
        return 'Valore non accettato per nessuno dei test';
    }))->validate(null);

    expect($validated->isValid())->toBeFalse();
    expect($validated->errors())->toBe([
        'or' => ['Valore non accettato per nessuno dei test'],
    ]);
});

test('allow-null', function () {
    $post = [
        'date' => null,
    ];
    $validated = V::group([
        'date' => V::or([
            V::date('Y-m-d H:i:s', O::continueOnFailure())->setValue(1)->asInt()->min(10),
            V::date('Y-m-d'),
            V::date('Y-m'),
            V::null()->setValue('yes!'),
        ]),
    ])->validate($post);

    expect($validated->value())->toBe(['date' => 'yes!']);
});

test('or-with-undefined-rule', function () {
    $validated = V::group([
        'name' => V::or([
            V::undefined()->setValue('undefined'),
            V::null()->setValue('null'),
            V::string()->setValue('string'),
        ]),
    ])->validate([
        'name' => 'niko',
    ]);

    expect($validated->value())->toBe(['name' => 'string']);
});

test('or-with-undefined-rule-2', function () {
    $v = V::group([
        'name' => V::undefined()->setValue('undefined'),
        'lastname' => V::or(
            [
                V::undefined()->setValue('undefined'),
                V::null()->setValue('null'),
                V::string()->setValue('string'),
            ],
            O::message(function (OrContext $oc) {
                $a = 1;
            })
        ),
    ]);

    $validated = $v->validate([
        'placeholder' => 'john',
        'lastname' => 'ked',
    ]);

    expect($validated->value())->toBe([
        'placeholder' => 'john',
        'lastname' => 'string',
        'name' => 'undefined',
    ]);
});

test('or-1', function () {
    $validated = V::or([
        V::string(),
        V::undefined(),
        V::null(),
    ])->validate(34);
    expect($validated->isValid())->toBe(false);
});

test('or-2', function () {
    $validated = V::or([
        V::undefined(),
        V::null(),
        V::date()->setValue('date'),
        V::string(),
    ])->validate('2000-01-01');
    expect($validated->isValid())->toBeTrue();
    expect($validated->value())->toBe('date');
});
