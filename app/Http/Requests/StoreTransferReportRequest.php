<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\File;

class StoreTransferReportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'receipt' => [
                'nullable',
                'file',
                File::types(['pdf', 'jpg', 'jpeg', 'png'])->max(5 * 1024),
            ],
        ];
    }
}
