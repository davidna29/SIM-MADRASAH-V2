<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserRole;
use Illuminate\Database\Seeder;

class UserRoleSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('username', 'admin')->first();
        if ($admin) {
            UserRole::create(['user_id' => $admin->id, 'role' => 'kepala_madrasah']);
        }

        $guruUmar = User::where('username', 'guru.umar')->first();
        if ($guruUmar) {
            UserRole::create(['user_id' => $guruUmar->id, 'role' => 'wakamad_kurikulum']);
        }

        $ibuAisy = User::where('username', 'ibu.aisy')->first();
        if ($ibuAisy) {
            UserRole::create(['user_id' => $ibuAisy->id, 'role' => 'tata_usaha']);
        }
    }
}
