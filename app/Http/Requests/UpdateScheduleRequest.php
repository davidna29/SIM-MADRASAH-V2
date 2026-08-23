<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateScheduleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return (new StoreScheduleRequest)->rules();
    }

    public function attributes(): array
    {
        return (new StoreScheduleRequest)->attributes();
    }
}
