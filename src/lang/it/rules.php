<?php

use Kedniko\VivyPluginStandard\Enum\RulesEnum;

return [
    'default' => [
        'generic' => 'Validazione fallita',
        'type' => 'Tipo errato',
        RulesEnum::ID_REQUIRED->value => 'Questo campo è obbligatorio',
        RulesEnum::ID_NOT_NULL->value => 'Questo campo non può essere null',
        RulesEnum::ID_NULL->value => 'Questo campo deve essere null',
        'notEmpty' => 'Questo campo non può essere vuoto',
        RulesEnum::ID_NOT_EMPTY_STRING->value => 'Questo campo non può essere una stringa vuota',
        'riceived' => 'Ricevuto',
        'minLength' => 'Valore troppo corto',
        'valuesNotAllowed' => 'Valori non ammessi',
        'match' => 'I campi non corrispondono',
    ],

    'string' => [
        'type' => 'Questa non è una stringa',
        RulesEnum::ID_REQUIRED->value => 'Questa stringa è obbligatoria',
        RulesEnum::ID_NOT_NULL->value => 'Questa stringa non può essere null',
        'notEmpty' => 'Questa string non può essere vuota',
        RulesEnum::ID_NOT_EMPTY_STRING->value => 'Questa stringa non può essere vuota',
        'minLength' => 'Stringa troppo corta',
        'maxLength' => 'Stringa troppo lunga',
        'length' => 'Lunghezza stringa non permessa',
        'min-2-letters-per-word' => 'Minino 2 lettere per parola (solo lettere)',
    ],
    'array' => [
        'type' => 'Questo non è un array',
        RulesEnum::ID_REQUIRED->value => 'Array obbligatorio',
        RulesEnum::ID_NOT_NULL->value => 'Questo campo non può essere null',
        'notEmpty' => 'Questo campo non può essere vuoto',
        RulesEnum::ID_NOT_EMPTY_STRING->value => 'Questo campo non può essere una stringa vuota',
    ],
    RulesEnum::ID_GROUP->value => [
        'type' => 'Questo non è un group',
        RulesEnum::ID_REQUIRED->value => 'Array obbligatorio',
        RulesEnum::ID_NOT_NULL->value => 'Questo campo non può essere null',
        'notEmpty' => 'Questo campo non può essere vuoto',
        RulesEnum::ID_NOT_EMPTY_STRING->value => 'Questo campo non può essere una stringa vuota',
    ],
    'bool' => [
        'type' => 'Tipo errato',
        RulesEnum::ID_REQUIRED->value => 'Valore obbligatorio',
    ],
    'number' => [
        'between' => 'Numero fuori range',
        'notBetween' => 'Numero fuori range',
        'min' => 'Numero troppo piccolo',
        'max' => 'Numero troppo grande',
    ],
    'float' => [
        'type' => 'float: Tipo errato',
        RulesEnum::ID_REQUIRED->value => 'Valore obbligatorio',
    ],
    'int' => [
        'type' => 'int: Tipo errato',
        RulesEnum::ID_REQUIRED->value => 'Valore obbligatorio',
    ],
    'intString' => [
        'type' => 'La stringa deve contenere un valore intero',
        RulesEnum::ID_REQUIRED->value => 'Valore obbligatorio',
    ],
    'intBool' => [
        'type' => 'Si accettano solo i valori 0 e 1',
        RulesEnum::ID_REQUIRED->value => 'Valore obbligatorio',
    ],
    'boolString' => [
        'type' => 'La stringa deve essere o "true" o "false"',
        RulesEnum::ID_REQUIRED->value => 'Valore obbligatorio',
    ],
    'date' => [
        'type' => 'La data non è valida',
        RulesEnum::ID_REQUIRED->value => 'Data obbligatoria',
        'min' => 'Data troppo lontana',
        'max' => 'Data troppo recente',
        'between' => 'Data non nell\'intervallo permesso',
        'notBetween' => 'Data non nell\'intervallo permesso',
    ],
    'email' => [
        'type' => 'Email non valida',
        RulesEnum::ID_REQUIRED->value => 'L\'email è obbligatoria',
        RulesEnum::ID_NOT_NULL->value => 'L\'email non può essere null',
        RulesEnum::ID_NOT_EMPTY_STRING->value => 'L\'email non può essere vuota',
    ],
    'phone' => [
        'type' => 'Telefono non valido',
        RulesEnum::ID_REQUIRED->value => 'Il telefono è obbliatorio',
        'notEmpty' => 'Il telefono non può essere vuoto',
    ],
];
