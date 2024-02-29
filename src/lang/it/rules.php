<?php

use Kedniko\Vivy\Enum\RulesEnum;

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
        'valuesNotAllowed' => 'Valori non ammessi',
        'match' => 'I campi non corrispondono',
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
];
