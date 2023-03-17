<?php

use Kedniko\Vivy\Rules;

return [
	'default' => [
		'generic'                  => 'Validazione fallita',
		'type'                     => 'Tipo errato',
		Rules::ID_REQUIRED         => 'Questo campo è obbligatorio',
		Rules::ID_NOT_NULL         => 'Questo campo non può essere null',
		Rules::ID_NULL             => 'Questo campo deve essere null',
		'notEmpty'                 => 'Questo campo non può essere vuoto',
		Rules::ID_NOT_EMPTY_STRING => 'Questo campo non può essere una stringa vuota',
		'riceived'                 => 'Ricevuto',
		'minLength'                => 'Valore troppo corto',
		'valuesNotAllowed'         => 'Valori non ammessi',
		'match'                    => 'I campi non corrispondono',
	],

	'string' => [
		'type'                     => 'Questa non è una stringa',
		Rules::ID_REQUIRED         => 'Questa stringa è obbligatoria',
		Rules::ID_NOT_NULL         => 'Questa stringa non può essere null',
		'notEmpty'                 => 'Questa string non può essere vuota',
		Rules::ID_NOT_EMPTY_STRING => 'Questa stringa non può essere vuota',
		'minLength'                => 'Stringa troppo corta',
		'maxLength'                => 'Stringa troppo lunga',
		'length'                   => 'Lunghezza stringa non permessa',
		'min-2-letters-per-word'   => 'Minino 2 lettere per parola (solo lettere)',
	],
	'array' => [
		'type'                     => 'Questo non è un array',
		Rules::ID_REQUIRED         => 'Array obbligatorio',
		Rules::ID_NOT_NULL         => 'Questo campo non può essere null',
		'notEmpty'                 => 'Questo campo non può essere vuoto',
		Rules::ID_NOT_EMPTY_STRING => 'Questo campo non può essere una stringa vuota',
	],
	Rules::ID_GROUP => [
		'type'                     => 'Questo non è un group',
		Rules::ID_REQUIRED         => 'Array obbligatorio',
		Rules::ID_NOT_NULL         => 'Questo campo non può essere null',
		'notEmpty'                 => 'Questo campo non può essere vuoto',
		Rules::ID_NOT_EMPTY_STRING => 'Questo campo non può essere una stringa vuota',
	],
	'bool' => [
		'type'             => 'Tipo errato',
		Rules::ID_REQUIRED => 'Valore obbligatorio',
	],
	'number' => [
		'between'    => 'Numero fuori range',
		'notBetween' => 'Numero fuori range',
		'min'        => 'Numero troppo piccolo',
		'max'        => 'Numero troppo grande',
	],
	'float' => [
		'type'             => 'float: Tipo errato',
		Rules::ID_REQUIRED => 'Valore obbligatorio',
	],
	'int' => [
		'type'             => 'int: Tipo errato',
		Rules::ID_REQUIRED => 'Valore obbligatorio',
	],
	'intString' => [
		'type'             => 'La stringa deve contenere un valore intero',
		Rules::ID_REQUIRED => 'Valore obbligatorio',
	],
	'intBool' => [
		'type'             => 'Si accettano solo i valori 0 e 1',
		Rules::ID_REQUIRED => 'Valore obbligatorio',
	],
	'boolString' => [
		'type'             => 'La stringa deve essere o "true" o "false"',
		Rules::ID_REQUIRED => 'Valore obbligatorio',
	],
	'date' => [
		'type'             => 'La data non è valida',
		Rules::ID_REQUIRED => 'Data obbligatoria',
		'min'              => 'Data troppo lontana',
		'max'              => 'Data troppo recente',
		'between'          => 'Data non nell\'intervallo permesso',
		'notBetween'       => 'Data non nell\'intervallo permesso',
	],
	'email' => [
		'type'                     => 'Email non valida',
		Rules::ID_REQUIRED         => 'L\'email è obbligatoria',
		Rules::ID_NOT_NULL         => 'L\'email non può essere null',
		Rules::ID_NOT_EMPTY_STRING => 'L\'email non può essere vuota',
	],
	'phone' => [
		'type'             => 'Telefono non valido',
		Rules::ID_REQUIRED => 'Il telefono è obbliatorio',
		'notEmpty'         => 'Il telefono non può essere vuoto',
	],
];
