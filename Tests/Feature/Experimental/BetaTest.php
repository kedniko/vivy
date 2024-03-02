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



test('return-group', function () {
    $v = V::group([
        'type' => [
            'number' => [
                'int' => V::group(function () {
                    return [
                        'value' => V::int(),
                    ];
                }),
            ],
        ],
    ]);

    $validated = $v->validate([
        'type' => [
            'number' => [
                'int' => [
                    'value' => 1,
                ],
            ],
        ],
    ]);

    expect($validated->isValid())->toBeTrue();
});










// test(
// 	'Login',
// 	function () {
// 		V::pratica('unipass', O::options()->message('nope'))->validate(1);

// 		$v = V::group([
// 			'name' => V::or([
// 				V::string()->notEmptyString()->minLength(3)->maxLength(10),
// 				V::emptyString()->setValue('niko'),
// 			]),
// 			'email'    => V::email(),
// 			'password' => V::string()->equals('aav')->catchAll('ciao'),
// 		]);
// 		$post = [
// 			'name'     => 'luca',
// 			'email'    => '',
// 			'password' => '',
// 		];
// 		$validated = $v->validate($post);
// 		$expectedErrors = [
// 			'email' => [
// 				'notEmptyString' => "L'email non può essere vuota",
// 			],
// 			// 'password' => [
// 			//     'notEmptyString' => "Questa stringa non può essere vuota"
// 			// ],
// 		];
// 		$expectedValue = [
// 			'name'     => 'luca',
// 			'email'    => '',
// 			'password' => 'ciao',
// 		];
// 		$this->assertEquals($validated->errors(), $expectedErrors, 'not equals errors');
// 		expect($validated->value())->tobe( $expectedValue, 'not expected value');
// 	}
// );

// test(
// 	'Custom',
// 	function () {
// 		// V::or([V::string()->min(3), null]);

// 		V::optional();
// 		S::notIn([
// 			S::string()->min(3),
// 		]);

// 		V::group([
// 			'age'  => V::inArray([1, 2, 3]),
// 			'nome' => V::notInArray(V::string()->length(11))->or([
// 				V::null(),
// 				V::string()->min(3)->max(10)->email(),
// 			])->not([
// 				V::equals('hey'),
// 				V::equals('ciao'),
// 			])->notEquals('hey')
// 				->notEquals('ciao')
// 				->string()->min(3)->max(10)
// 				->or([
// 					V::string()->min(5)->setValue('ciao min 5'),
// 					V::number()->min(5)->setValue('ciao min 5'),
// 				], O::options()->message('Non è valido')),
// 			'nome1' => V::allowNull(false)->string()->min(3),
// 			'nome2' => V::or([V::null(), false], )->string()->min(3),
// 			'nome3' => V::or([null, false], )->string()->min(3),
// 			'nome4' => V::or([null, '', false], )->string()->min(3),
// 		]);

// 		V::or([
// 			V::string()->min(3),
// 			V::any(),
// 			V::any(),
// 			V::never(),
// 			V::empty(),
// 			V::null(),
// 			V::true(),
// 			V::false(),
// 			V::emptyString(),
// 		])->or([], $continue = false)->deny([
// 			V::string()->min(3),
// 			V::any(),
// 			V::any(),
// 			V::never(),
// 			V::empty(),
// 			V::null(),
// 			V::emptyString(),
// 		]);

// 		V::any()->allowEmptyString()->optional();
// 		$validated = V::email(O::message('no'))->domainIs('a.com')->validate('a@a.com');
// 	}
// );

// test(
// 	'IsValid',
// 	function () {
// 		$v = V::group([
// 			'name' => V::string(),
// 			'num'  => V::or([
// 				V::string(),
// 				V::int()->min(3000)->catchAll('sono intero'),
// 			]),
// 			'count' => V::or([
// 				V::int(),
// 				V::string(),
// 				V::number()->addTransformer(V::transformer('1', function (GroupContext $gc) {
// 					return $gc->value + 10;
// 				}))->toString()->setValue(function (Context $c) {
// 					return $c->value . '_edit';
// 				}),
// 			]),
// 			'address' => [
// 				'via'   => V::string(),
// 				'citta' => V::string(),
// 				'stato' => V::string()->maxLength(2),
// 			],
// 			'numbers' => V::array()->minCount(9)->maxCount(11)->each(V::int()),
// 			'emails'  => V::array()->each([
// 				'nominativo' => V::string()->transformToUpperCase(),
// 				'email'      => V::email()->domainInArray(['example.com']),
// 				'main'       => V::bool()->equals(true)->optional(function () {
// 					return 'yes';
// 				}),
// 			]),
// 		]);

// 		$post = [
// 			'name'    => 'niko',
// 			'num'     => 1,
// 			'count'   => 21.2,
// 			'address' => [
// 				'via'   => 'main street',
// 				'citta' => 'new York',
// 				'stato' => 'PL',
// 			],
// 			'numbers' => [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11],
// 			'emails'  => [
// 				[
// 					'nominativo' => 'niko',
// 					'email'      => 'niko@example.com',
// 					'main'       => true,
// 				],
// 				[
// 					'nominativo' => 'luca',
// 					'email'      => 'luca@example.com',
// 				],
// 			],
// 		];

// 		$validated = $v->validate($post);

// 		$expected = [
// 			'name'    => 'niko',
// 			'num'     => 'sono intero',
// 			'count'   => '31.2_edit',
// 			'address' => [
// 				'via'   => 'main street',
// 				'citta' => 'new York',
// 				'stato' => 'PL',
// 			],
// 			'numbers' => [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11],
// 			'emails'  => [
// 				[
// 					'nominativo' => 'NIKO',
// 					'email'      => 'niko@example.com',
// 					'main'       => true,
// 				],
// 				[
// 					'nominativo' => 'LUCA',
// 					'email'      => 'luca@example.com',
// 					'main'       => 'yes',
// 				],
// 			],
// 		];

// 		expect($validated->isValid())->toBeFalse(), 'Non è valid');
// 		$this->assertEquals($expected, $validated->value(), 'Non è expected');
// 	}
// );

// test(
// 	'1',
// 	function () {
// 		for ($i = 0; $i < 50000; $i++) {
// 			$this->value[0] = 1;
// 		}
// 	}
// );

// test(
// 	'2',
// 	function () {
// 		for ($i = 0; $i < 50000; $i++) {
// 			$this->context->value[0] = 1;
// 		}
// 	}
// );

// test(
// 	'Plugins',
// 	function () {
// 		v::pratica()->nonScaduta();
// 		V::email();
// 	}
// );

// test('function-group', function () {
// 		$v = V::group([
// 			'int' => V::group(function (GroupContext $c) {
// 				$value = $c->value;
// 				if (isset($value['value']) && $value['value'] === 1) {
// 					$c->appendField('nums', V::int());
// 				}
// 				return [
// 					'value1' => V::int(),
// 					'value2' => V::int(),
// 					'value3' => V::int(),
// 				];
// 			}),
// 			// 'nums' => V::int(),
// 		]);

// 		// $v = V::group([
// 		//     'int' => V::group([
// 		//         'value' => V::int(),
// 		//     ]),
// 		// ]);

// 		$validated = $v->validate([
// 			'int' => [
// 				'value' => 1,
// 			],
// 		]);
// 		$d = 1;
// 	});



// test(
// 	'DefaultValue',
// 	function () {
// 		$v = V::group([
// 			// 'name' => V::int()->catchErrorsAndSetValue('John'),
// 			// 'surname' => V::string()->optional(function (Context $c) {
// 			//     return 'Doe';
// 			// }),
// 			'price' => V::int()->catchAll(function (GroupContext $c) {
// 				return 100;
// 			}),
// 			'age' => V::everything()->notNull()->notEmptyString()->catchAll(20),
// 		]);

// 		$post = [
// 			'name1' => null,
// 		];
// 		$validated = $v->validate($post);

// 		$expected = [
// 			'name1' => null,
// 			// 'name' => 'John',
// 			// 'surname' => 'Doe',
// 			'price' => 100,
// 			'age'   => 20,
// 		];
// 		expect($validated->value())->tobe( $expected, 'not equals');

// 		// dd([$validated->value()]);
// 	}
// );


// test(
// 	'Refactor',
// 	function () {
// 		$v = V::group([
// 			'email' => V::email()->asGroup(),
// 			'test'  => V::string(),
// 		]);
// 		$post = [
// 			'email'    => '',
// 			'password' => '',
// 		];
// 		$validated = $v->validate($post);
// 		$d = 1;
// 	}
// );

// private function runAllMethods($methods = null)
// {
// 	if (!$methods) {
// 		$methodsChild = get_class_methods($this);
// 		// $methodsParent = get_class_methods(CustomTestCase::class);
// 		$methods = array_filter($methodsChild, function ($method) {
// 			return strpos($method, 'test') === 0;
// 		);
// 	}
// 	foreach ($methods as $methodname) {
// 		if ($methodname && $methodname !== '__construct') {
// 			// echo ColorCli::color("Running " . $methodname . "\n", self::WHITE);
// 			$this->onBefore($methodname);

// 			$this->startPerformance();
// 			$this->{$methodname}();
// 			$this->endPerformance();

// 			$this->onAfter();
// 		}
// 	}
// }
