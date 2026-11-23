<?php

namespace App\Http\Requests\Api\V1;

use App\Models\MediaItem;
use Closure;
use Illuminate\Foundation\Http\FormRequest;

class UpdateGalleryItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:150'],
            'deskripsi' => ['nullable', 'string', 'max:500'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'video_url' => [
                'nullable',
                'string',
                'max:255',
                function (string $attribute, mixed $value, Closure $fail): void {
                    $item = $this->route('item');

                    if ($value === null || $value === '' || ! $item instanceof MediaItem) {
                        return;
                    }

                    if ((string) $value !== (string) $item->video_url) {
                        $fail('video_url tidak dapat diubah melalui API.');
                    }
                },
            ],
        ];
    }

    public function attributes(): array
    {
        return [
            'title' => 'judul',
            'deskripsi' => 'deskripsi',
            'sort_order' => 'urutan',
            'video_url' => 'tautan video',
        ];
    }
}
