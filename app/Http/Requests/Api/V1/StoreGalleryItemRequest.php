<?php

namespace App\Http\Requests\Api\V1;

use Closure;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreGalleryItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $isPhoto = fn (): bool => $this->input('tipe') === 'foto';
        $isVideo = fn (): bool => $this->input('tipe') === 'video';

        return [
            'title' => ['required', 'string', 'max:150'],
            'deskripsi' => ['nullable', 'string', 'max:500'],
            'tipe' => ['required', Rule::in(['foto', 'video'])],
            'file' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:4096',
                Rule::requiredIf($isPhoto),
                Rule::prohibitedIf($isVideo),
            ],
            'video_url' => [
                'nullable',
                'string',
                'max:255',
                Rule::requiredIf($isVideo),
                Rule::prohibitedIf($isPhoto),
                function (string $attribute, mixed $value, Closure $fail): void {
                    if ($value === null || $value === '') {
                        return;
                    }

                    if (! preg_match('#^https?://(?:www\.|m\.)?youtube\.com/watch\?v=([A-Za-z0-9_-]{11})(?:[&?].*)?$#i', (string) $value)
                        && ! preg_match('#^https?://(?:www\.)?youtu\.be/([A-Za-z0-9_-]{11})(?:\?.*)?$#i', (string) $value)) {
                        $fail('video_url hanya boleh tautan YouTube (youtube.com/watch?v=… atau youtu.be/…).');
                    }
                },
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'file.required' => 'File foto wajib diunggah untuk tipe foto.',
            'file.prohibited' => 'File tidak boleh diunggah untuk tipe video.',
            'video_url.required' => 'video_url wajib diisi untuk tipe video.',
            'video_url.prohibited' => 'video_url tidak boleh diisi untuk tipe foto.',
        ];
    }

    public function attributes(): array
    {
        return [
            'title' => 'judul',
            'deskripsi' => 'deskripsi',
            'tipe' => 'tipe media',
            'file' => 'file foto',
            'video_url' => 'tautan video',
        ];
    }
}
