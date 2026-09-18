<?php

declare(strict_types=1);

return [
    'accepted' => 'Vous devez accepter :attribute.',
    'array' => 'Le champ :attribute doit être un tableau.',
    'email' => ':attribute n\'est pas une adresse e-mail valide.',
    'in' => 'Le :attribute sélectionné est invalide.',
    'integer' => ':attribute doit être un nombre entier.',
    'max' => [
        'array' => 'Le champ :attribute ne doit pas contenir plus de :max éléments.',
        'numeric' => 'Le champ :attribute ne doit pas être supérieur à :max.',
        'string' => 'Le champ :attribute ne doit pas dépasser :max caractères.',
    ],
    'min' => [
        'array' => 'Le champ :attribute doit contenir au moins :min éléments.',
        'numeric' => 'Le champ :attribute doit être au moins :min.',
        'string' => 'Le champ :attribute doit contenir au moins :min caractères.',
    ],
    'required' => 'Le champ :attribute est obligatoire.',
    'string' => ':attribute doit être une chaîne de caractères.',
    'unique' => 'La valeur du champ :attribute est déjà utilisée.',

    'custom' => [
        'email' => [
            'required' => 'Veuillez renseigner votre adresse e-mail.',
            'email' => 'Cette adresse e-mail ne semble pas valide. Vérifiez s\'il n\'y a pas de faute de frappe.',
            'max' => 'Cette adresse e-mail est particulièrement longue. Est-elle correcte ?',
        ],
        'organisation' => [
            'required' => 'Veuillez renseigner votre organisation.',
        ],
        'type' => [
            'required' => 'Veuillez sélectionner un type d\'organisation.',
            'in' => 'Veuillez sélectionner un type d\'organisation dans la liste.',
        ],
    ],

    'attributes' => [
        'email' => 'votre adresse e-mail',
        'name' => 'votre nom',
        'place' => 'votre commune ou code postal',
        'motivation' => 'votre motivation',
        'message' => 'votre message',
        'phone' => 'votre numéro de téléphone',
        'organisation' => 'votre organisation',
        'type' => 'le type d\'organisation',
    ],
];
