<?php

return [
    'after' => 'Le champ :attribute doit être après le :date.',
    'after_or_equal' => 'Le champ :attribute doit être égal ou après le :date.',
    'date_format' => 'Le champ :attribute doit respecter le format :format.',
    'email' => 'Le champ :attribute doit être une adresse e-mail valide.',
    'in' => 'La valeur sélectionnée pour :attribute est invalide.',
    'integer' => 'Le champ :attribute doit être un nombre entier.',
    'max' => [
        'numeric' => 'Le champ :attribute ne peut pas dépasser :max.',
        'string' => 'Le champ :attribute ne peut pas dépasser :max caractères.',
    ],
    'min' => [
        'numeric' => 'Le champ :attribute doit être au moins égal à :min.',
    ],
    'required' => 'Le champ :attribute est obligatoire.',
    'string' => 'Le champ :attribute doit être du texte.',
    'attributes' => [
        'guest_name' => 'nom complet',
        'guest_email' => 'adresse e-mail',
        'guest_phone' => 'téléphone',
        'room_type' => 'type de chambre',
        'check_in' => 'date d’arrivée',
        'check_out' => 'date de départ',
        'guests' => 'nombre de voyageurs',
        'special_requests' => 'demandes particulières',
    ],
];
