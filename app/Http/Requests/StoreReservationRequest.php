<?php

namespace App\Http\Requests;

use App\Models\Reservation;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreReservationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'guest_name' => ['required', 'string', 'max:120'],
            'guest_email' => ['required', 'string', 'email', 'max:254'],
            'guest_phone' => ['nullable', 'string', 'max:40'],
            'room_type' => ['required', 'string', Rule::in(array_keys(Reservation::ROOM_CAPACITIES))],
            'check_in' => ['required', 'date_format:Y-m-d', 'after_or_equal:today'],
            'check_out' => ['required', 'date_format:Y-m-d', 'after:check_in'],
            'guests' => ['required', 'integer', 'min:1', 'max:4'],
            'special_requests' => ['nullable', 'string', 'max:2000'],
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator): void {
                $roomType = $this->input('room_type');
                $capacity = is_string($roomType)
                    ? (Reservation::ROOM_CAPACITIES[$roomType] ?? null)
                    : null;

                if (
                    $capacity !== null
                    && ! $validator->errors()->has('guests')
                    && (int) $this->input('guests') > $capacity
                ) {
                    $validator->errors()->add(
                        'guests',
                        __('site.reservation.errors.capacity', ['capacity' => $capacity]),
                    );
                }
            },
        ];
    }
}
