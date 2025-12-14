<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OperationLatestRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'limit' => ['integer', 'min:1', 'max:50'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
