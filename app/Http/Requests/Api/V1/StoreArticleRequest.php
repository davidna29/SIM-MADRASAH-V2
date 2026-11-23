<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class StoreArticleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:200'],
            'slug' => ['nullable', 'string', 'max:220', 'unique:articles,slug'],
            'summary' => ['nullable', 'string', 'max:300'],
            'body' => ['required', 'string'],
            'category' => ['nullable', 'string', 'max:50'],
            'tags' => ['nullable', 'string', 'max:255'],
            'featured_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'status' => ['prohibited'],
            'published_at' => ['prohibited'],
        ];
    }

    public function messages(): array
    {
        return [
            'status.prohibited' => 'Status tidak boleh dikirim; berita API selalu dibuat sebagai draft.',
            'published_at.prohibited' => 'Berita baru selalu berstatus draft; publikasikan melalui PUT dengan status publish.',
        ];
    }
}
