<?php

return [
    'after' => 'The :attribute must be a date after :date.',
    'after_or_equal' => 'The :attribute must be a date after or equal to :date.',
    'date_format' => 'The :attribute must match the format :format.',
    'email' => 'The :attribute must be a valid email address.',
    'in' => 'The selected :attribute is invalid.',
    'integer' => 'The :attribute must be an integer.',
    'max' => [
        'numeric' => 'The :attribute may not be greater than :max.',
        'string' => 'The :attribute may not be greater than :max characters.',
    ],
    'min' => [
        'numeric' => 'The :attribute must be at least :min.',
    ],
    'required' => 'The :attribute field is required.',
    'string' => 'The :attribute must be a string.',
    'attributes' => [
        'guest_name' => 'full name',
        'guest_email' => 'email address',
        'guest_phone' => 'phone',
        'room_type' => 'room type',
        'check_in' => 'check-in date',
        'check_out' => 'check-out date',
        'guests' => 'number of guests',
        'special_requests' => 'special requests',
    ],
];
