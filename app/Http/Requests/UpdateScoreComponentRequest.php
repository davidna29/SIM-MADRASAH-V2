<?php

namespace App\Http\Requests;

use App\Models\AcademicYear;
use App\Models\ScoreComponent;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class UpdateScoreComponentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:60'],
            'weight' => ['required', 'numeric', 'min:0', 'max:100'],
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator) {
                $component = $this->route('scoreComponent');
                $tahun = AcademicYear::active();
                $name = $this->input('name');
                $weight = $this->input('weight');

                if (! $tahun || ! $component) {
                    return;
                }

                if ($name && ScoreComponent::where('academic_year_id', $tahun->id)
                    ->where('name', $name)
                    ->where('id', '!=', $component->id)
                    ->exists()) {
                    $validator->errors()->add('name', 'Nama komponen sudah digunakan pada tahun ajaran ini.');
                }

                $currentTotal = (float) ScoreComponent::where('academic_year_id', $tahun->id)
                    ->where('id', '!=', $component->id)
                    ->sum('weight');
                $newTotal = $currentTotal + (float) ($weight ?? 0);

                if (abs($newTotal - 100) > 0.001) {
                    $validator->errors()->add('weight', "Total bobot komponen harus 100% (saat ini akan menjadi {$newTotal}%).");
                }
            },
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'nama komponen',
            'weight' => 'bobot',
        ];
    }
}
