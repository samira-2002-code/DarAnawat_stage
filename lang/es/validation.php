<?php

return [
    'after' => 'El campo :attribute debe ser una fecha posterior a :date.',
    'after_or_equal' => 'El campo :attribute debe ser igual o posterior a :date.',
    'date_format' => 'El campo :attribute debe tener el formato :format.',
    'email' => 'El campo :attribute debe ser una dirección de correo válida.',
    'in' => 'El valor seleccionado para :attribute no es válido.',
    'integer' => 'El campo :attribute debe ser un número entero.',
    'max' => [
        'numeric' => 'El campo :attribute no puede ser mayor que :max.',
        'string' => 'El campo :attribute no puede superar :max caracteres.',
    ],
    'min' => [
        'numeric' => 'El campo :attribute debe ser al menos :min.',
    ],
    'required' => 'El campo :attribute es obligatorio.',
    'string' => 'El campo :attribute debe ser texto.',
    'attributes' => [
        'guest_name' => 'nombre completo',
        'guest_email' => 'correo electrónico',
        'guest_phone' => 'teléfono',
        'room_type' => 'tipo de habitación',
        'check_in' => 'fecha de llegada',
        'check_out' => 'fecha de salida',
        'guests' => 'número de huéspedes',
        'special_requests' => 'solicitudes especiales',
    ],
];
