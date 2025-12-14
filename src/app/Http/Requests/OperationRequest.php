<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OperationRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'page' => ['integer', 'min:1'],
            'per_page' => ['integer', 'min:1', 'max:100'],
            'sort' => ['in:date'],
            'dir' => ['in:asc,desc'],
            'q' => ['string'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
