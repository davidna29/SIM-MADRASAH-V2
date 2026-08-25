<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $userId = $this->route('user');

        return [
            'name' => ['required', 'string', 'max:100'],
            'username' => ['nullable', 'string', 'max:30', 'unique:users,username,'.$userId->id],
            'email' => ['required', 'email', 'max:100', 'unique:users,email,'.$userId->id],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'role' => ['required', 'string', 'in:super_admin,guru,orang_tua,siswa,kepala_madrasah,wakamad_kurikulum,wakamad_kesiswaan,wakamad_humas,wakamad_sarpras,bendahara,tata_usaha,editor_berita,guru_bk'],
            'student_id' => ['nullable', 'exists:students,id'],
            'additional_roles' => ['nullable', 'array'],
            'additional_roles.*' => ['string', 'in:guru,orang_tua,siswa,kepala_madrasah,wakamad_kurikulum,wakamad_kesiswaan,wakamad_humas,wakamad_sarpras,bendahara,tata_usaha,editor_berita,guru_bk'],
        ];
    }

    public function attributes(): array
    {
        return (new StoreUserRequest)->attributes();
    }
}
