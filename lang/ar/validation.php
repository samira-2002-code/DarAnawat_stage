<?php

return [
    'after' => 'يجب أن يكون :attribute تاريخاً بعد :date.',
    'after_or_equal' => 'يجب أن يكون :attribute مساوياً أو بعد :date.',
    'date_format' => 'يجب أن يتبع :attribute التنسيق :format.',
    'email' => 'يجب أن يكون :attribute عنوان بريد إلكتروني صالحاً.',
    'in' => 'القيمة المختارة لـ :attribute غير صالحة.',
    'integer' => 'يجب أن يكون :attribute عدداً صحيحاً.',
    'max' => [
        'numeric' => 'يجب ألا تتجاوز قيمة :attribute :max.',
        'string' => 'يجب ألا يتجاوز :attribute :max حرفاً.',
    ],
    'min' => [
        'numeric' => 'يجب ألا تقل قيمة :attribute عن :min.',
    ],
    'required' => 'حقل :attribute مطلوب.',
    'string' => 'يجب أن يكون :attribute نصاً.',
    'attributes' => [
        'guest_name' => 'الاسم الكامل',
        'guest_email' => 'البريد الإلكتروني',
        'guest_phone' => 'الهاتف',
        'room_type' => 'نوع الغرفة',
        'check_in' => 'تاريخ الوصول',
        'check_out' => 'تاريخ المغادرة',
        'guests' => 'عدد الضيوف',
        'special_requests' => 'الطلبات الخاصة',
    ],
];
