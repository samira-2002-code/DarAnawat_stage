<?php

namespace App\Http\Requests;

use App\Models\Reservation;
use App\Models\Room;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class OwnerRoomRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isOwner() ?? false;
    }

    public function rules(): array
    {
        $room = $this->route('room');

        return [
            'room_number' => [
                'required',
                'string',
                'max:40',
                Rule::unique('rooms', 'room_number')->ignore($room instanceof Room ? $room->getKey() : null),
            ],
            'room_type' => ['required', 'string', Rule::in(array_keys(Reservation::ROOM_CAPACITIES))],
            'price_per_night' => ['required', 'integer', 'min:1', 'max:100000000'],
            'status' => ['required', 'string', Rule::in(Room::STATUSES)],
        ];
    }
}
