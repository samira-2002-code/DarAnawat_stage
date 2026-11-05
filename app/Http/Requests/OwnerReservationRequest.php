<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class OwnerReservationRequest extends StoreReservationRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isOwner() ?? false;
    }

    public function rules(): array
    {
        return [
            ...parent::rules(),
            'user_id' => [
                'nullable',
                'integer',
                Rule::exists('users', 'id')->where('role', 'customer'),
            ],
        ];
    }
}
