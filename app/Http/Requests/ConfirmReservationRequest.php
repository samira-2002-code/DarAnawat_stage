<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
            'room_id' => ['nullable', 'integer', Rule::exists('rooms', 'id')],
            'reason' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
