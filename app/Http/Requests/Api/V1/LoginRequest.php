<?php

namespace App\Http\Requests\Api\V1;

use App\Support\Api\ApiScope;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'login' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string'],
            'device_name' => ['required', 'string', 'max:100'],
            'scopes' => ['nullable', 'array'],
            'scopes.*' => ['string', Rule::in(ApiScope::ALL)],
        ];
    }
}
