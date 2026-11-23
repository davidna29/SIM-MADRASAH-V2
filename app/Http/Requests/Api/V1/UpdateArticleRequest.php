<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateArticleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $articleId = $this->route('article')?->id;

        return [
            'title' => ['required', 'string', 'max:200'],
            'slug' => ['nullable', 'string', 'max:220', Rule::unique('articles', 'slug')->ignore($articleId)],
            'summary' => ['nullable', 'string', 'max:300'],
            'body' => ['required', 'string'],
            'category' => ['nullable', 'string', 'max:50'],
            'tags' => ['nullable', 'string', 'max:255'],
            'featured_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'status' => ['prohibited'],
        ];
    }
}
