<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ConfirmReservationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isOwner() ?? false;
    }

    public function rules(): array
    {
        return [
            'availability_confirmed' => ['required', 'accepted'],
            'reason' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
