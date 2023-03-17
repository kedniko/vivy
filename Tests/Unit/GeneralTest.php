<?php

namespace Tests;

use App\App;
use Kedniko\Vivy\ArrayContext;
use Kedniko\Vivy\Context;
use Kedniko\Vivy\Core\GroupContext;
use Kedniko\Vivy\O;
use Kedniko\Vivy\V;

uses()->group('general');

beforeAll(function () {
	App::boot();
});

test('required-if-simple-1', function () {
	$v = V::group([
		'allow_sms' => V::bool(),
		'phone'     => V::requiredIf(true),
	]);
	$validated = $v->validate(['allow_sms' => false, ]);
	expect($validated->isValid())->toBeFalse();

	$validated = $v->validate(['allow_sms' => true, ]);
	expect($validated->isValid())->toBeFalse();
});

test('required-if-simple-2', function () {
	$v = V::group([
		'allow_sms' => V::bool(),
		'phone'     => V::requiredIf(false)->string(),
	]);
	$validated = $v->validate(['allow_sms' => false, ]);
	expect($validated->isValid())->toBeTrue();
});

test('required-if-2', function () {
	$v = V::group([
		'allow_sms' => V::bool()->required(),
		'phone'     => V::requiredIf(function (Context $c) {
			$allowSmsContext = $c->fatherContext->getFieldContext('allow_sms');
			$res = $allowSmsContext->value === true;
			return $res;
		}),
	]);
	$validated = $v->validate(['allow_sms' => false, ]);
	expect($validated->isValid())->toBeTrue();

	$validated = $v->validate(['allow_sms' => true, ]);
	expect($validated->isValid())->toBeFalse();
});

test('required-if-3', function () {
	$v = V::group([
		'allow_sms' => V::bool()->required(),
		'phone'     => V::requiredIfField('allow_sms', function (Context $c) {
			$res = $c->value === true;
			return $res;
		}),
	]);
	$validated = $v->validate(['allow_sms' => false, ]);
	expect($validated->isValid())->toBeTrue();

	$validated = $v->validate(['allow_sms' => true, ]);
	expect($validated->isValid())->toBeFalse();
});

test('simple-setvalue', function () {
	$v = V::string()->setValue('ok!');
	$validated = $v->validate('');
	expect($validated->isValid())->toBeTrue();
	expect($validated->value())->tobe('ok!');
});
test('simple-setvalue-2', function () {
	$v = V::group([
		'confirmed' => V::or([
			V::bool(),
			V::undefined()->setValue(false),
		]),
	]);
	$validated = $v->validate([]);
	expect($validated->isValid())->toBeTrue();
});

test('real-input', function () {
	$validated = V::group([
		'sale_group'    => V::string(true, true, true, O::continueOnFailure())->asAny()->int(),
		'sale_id'       => V::string(),
		'exclude_taxes' => V::bool(),
		'dates'         => V::array()->maxCount(5)->each(V::date('Y-m-d')),
		'rows'          => V::array()->each([
			'discount'     => V::float(),
			'duration'     => V::int(),
			'operator_id'  => V::string(),
			'price_single' => V::number(),
			'product_id'   => V::string(),
			'quantity'     => V::int(),
		]),
	])->validate([
		'sale_group'    => 21.1,
		'id'            => null,
		'exclude_taxes' => true,
		'sale_dates'    => [],
		'rows'          => [
			[
				'discount'     => 21.21,
				'duration'     => 0,
				'operator_id'  => '4386578437465',
				'price_single' => 20.00,
				'product_id'   => '754389758',
				'quantity'     => 4,
			],
		],
	]);

	$expectedErrors = [
		'sale_group' => [
			'string' => ['Validazione fallita'],
			'int'    => ['Validazione fallita'],
		],
		'sale_id' => [
			'required' => ['Questo campo è obbligatorio'],
		],
		'dates' => [
			'required' => ['Questo campo è obbligatorio'],
		],
	];

	expect($validated->isValid())->toBeFalse();
	expect($validated->errors())->toBe($expectedErrors);
});

test('empty-string', function () {
	$v = V::emptyString();
	$validated = $v->validate('');
	expect($validated->isValid())->toBeTrue();
	expect($validated->value())->tobe('');
});

test('simple', function () {
	$v = V::string()->length(4);
	$validated = $v->validate('1234');
	expect($validated->isValid())->toBeTrue();
});

test('group', function () {
	$v = V::group([
		'nome'    => V::string()->minLength(4)->maxLength(10),
		'cognome' => V::string()->length(5),
		'age'     => V::int()->min(18)->max(99),
	]);
	$validated = $v->validate([
		'nome'    => '1234567890',
		'cognome' => '12345',
		'age'     => 99,
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

/**
 * @noinspection
 */
test('performance-native', function () {
	if (function_exists('xdebug_is_debugger_active')) {
		$isDebugging = call_user_func('xdebug_is_debugger_active');
		if ($isDebugging) {
			expect(true)->toBe(false, 'Xdebug is active, please disable it for performance tests');
			return;
		};
	}

	$hrtime_start = hrtime(true);
	$r = range(1, 50000);
	$post = [
		'num' => $r,
	];
	$l = count($r);
	for ($i = 1; $i < $l; $i++) {
		is_int($post['num'][$i]);
	}
	$ms = (hrtime(true) - $hrtime_start) / 1e+6;
	// echo('performance-native: ' . $ms) . ' ms' . PHP_EOL;
	expect($ms)->toBeLessThan(20);
})->skip();

test('performance-vivy', function () {
	if (function_exists('xdebug_is_debugger_active')) {
		$isDebugging = call_user_func('xdebug_is_debugger_active');
		if ($isDebugging) {
			expect(true)->toBe(false, 'Xdebug is active, please disable it for performance tests');
			return;
		};
	}

	$hrtime_start = hrtime(true);
	$r = range(1, 50000);
	$post = [
		'num' => $r,
	];
	$v = V::group(['num' => V::array()->each(V::int()), ]);
	$validated = $v->validate($post);
	expect($validated->isValid())->toBeTrue();
	$ms = (hrtime(true) - $hrtime_start) / 1e+6;
	// echo('performance-vivy: ' . $ms . ' ms') . PHP_EOL;
	expect($ms)->toBeLessThan(2000);
})->skip();

// test('performance-with-functions', function () {
// 		// $isInt = function ($value) {
// 		//     return is_int($value);
// 		// };

// 		$class = \Kedniko\Vivy\Rules\RuleFunctions::class;
// 		$method = 'int';

// 		$r = range(1, 50000);
// 		$post = [
// 			'num' => $r,
// 		];
// 		$l = count($r);
// 		$c = new Context();
// 		for ($i = 1; $i < $l; $i++) {
// 			for ($j = 0; $j < 300; $j++) {
// 				$a = 1;
// 			}
// 			$c->value = $post['num'][$i];
// 			$class::$method($c);

// 			// call static strin function in php 5
// 		}
// 	});

// test(
// 	'PerformanceVivyOptimized',
// 	function () {
// 		$r = range(1, 50000);
// 		$post = [
// 			'num' => $r,
// 		];
// 		$v = V::group([
// 			'num' => V::array()->each(
// 				V::int(O::options()->ignoreContext())
// 			),
// 		]);
// 		$this->startPerformance();
// 		$validated = $v->validate($post);
// 		expect($validated->isValid())->toBeTrue();
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

test('dynamic', function () {
	$v = V::group(function ($c) {
		return [
			'address' => [
				'via'   => V::string(),
				'citta' => V::string()->toUppercase(),
				'stato' => V::string()->maxLength(2),
			],
			'status' => V::null()->onValid(function (GroupContext $c) {
				$c->appendField('count', V::int());
				$c->appendFieldAfterCurrent('numbers', V::array()->count(10)->minCount(9)->maxCount(11)->each(V::int()));
			}),
		];
	});
	$v = V::group([
		'address' => [
			'via'   => V::string(),
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
		'name'    => 'niko',
		'count'   => 340,
		'address' => [
			'via'   => 'main street',
			'citta' => 'new York',
			'stato' => 'PL',
		],
		'numbers' => [1, 2, 3, 4, 5, 6, 7, 8, 9, 10],
		'status'  => null,
	]);

	$expected = [
		'name'    => 'niko',
		'count'   => 340,
		'address' => [
			'via'   => 'main street',
			'citta' => 'NEW YORK',
			'stato' => 'PL',
		],
		'numbers' => [1, 2, 3, 4, 5, 6, 7, 8, 9, 10],
		'status'  => null,
	];

	$isvalid = $validated->isValid();

	expect($validated->value())->tobe($expected, 'Validated post non è expected');
	expect($isvalid)->toBeTrue();
});

test('valid-files', function () {
	$v = V::group([
		'file' => V::files()
			->maxCount(3)
			->maxTotalSize(50, 'MB')
			->each(V::file()
					->mime('application/javascript', O::ifArrayIndex(0))
					->mime('application/octet-stream', O::ifArrayIndex(1))
					->extensionIn(['js', 'phar'])
					->tap(function (ArrayContext $c) {
						if ($c->getIndex() === 0) {
							$c->getField()->asFile()->size(631802 + 1, 'B', O::message('Voglio 631802 B')->once());
						}
						if ($c->getIndex() === 1) {
							$c->getField()->asFile()->maxSize(1, 'B', O::once()->continueOnFailure()->appendAfterCurrent()->message('Voglio max 1 B'));
						}
					})
					->minSize(100, 'MB', O::options()->message('Voglio 100 MB')->continueOnFailure())),
		'file2' => V::file()->minSize(300, 'MB', O::options()->message('Voglio almeno 300 MB')),
	]);

	$validated = $v->validate([
		'file' => [
			'name' => [
				0 => '.storage/vue.global.js',
				1 => '.storage/phpunit-4.8.36.phar',
			],
			'full_path' => [
				0 => '.storage/vue.global.js',
				1 => '.storage/phpunit-4.8.36.phar',
			],
			'type' => [
				0 => 'application/javascript',
				1 => 'application/octet-stream',
			],
			'tmp_name' => [
				0 => '.storage/vue.global.js',
				1 => '.storage/phpunit-4.8.36.phar',
			],
			'error' => [
				0 => 0,
				1 => 0,
			],
			'size' => [
				0 => 631802,
				1 => 3100908,
			],
		],
		'file2' => [
			'name'      => 'vue.global.prod.js',
			'full_path' => 'vue.global.prod.js',
			'type'      => 'application/javascript',
			'tmp_name'  => '.storage/vue.global.prod.js',
			'error'     => 0,
			'size'      => 127427,
		],
	]);

	$expectedErrors = [
		'file' => [
			[
				'minSize' => ['Voglio 100 MB'],
				'size'    => ['Voglio 631802 B'],
			],
			[
				'maxSize' => ['Voglio max 1 B'],
				'minSize' => ['Voglio 100 MB'],
			],
		],
		'file2' => [
			'minSize' => ['Voglio almeno 300 MB'],
		],
	];

	expect($validated->errors())->toBe($expectedErrors);
});

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

test('cast', function () {
	expect((array)1)->toBe([1]);
});

test('login-1', function () {
	$v = V::group([
		'name' => V::string()->addTransformer(function (GroupContext $gc) {
			$value = $gc->value;
			return strtoupper($value);
		}),
		'password' => V::string(),
	]);

	$validated = $v->validate([
		'name' => 'niko',
	]);

	expect($validated->value())->tobe([
		'name' => 'NIKO',
	]);

	expect($validated->errors())->toBe([
		'password' => [
			'required' => ['Questo campo è obbligatorio'],
		],
	]);
});

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
