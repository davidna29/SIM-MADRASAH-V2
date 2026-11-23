<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $this->call([
            SettingSeeder::class,
            MisnuSettingSeeder::class,
            PublicPageSeeder::class,
            MisnuPublicPageSeeder::class,
            WalkingSkeletonSeeder::class,
            KepegawaianSeeder::class,
            GuruMISeeder::class,
            MisnuEmployeeSeeder::class,
            KehadiranPegawaiSeeder::class,
            RoomSeeder::class,
            MisnuRoomSeeder::class,
            AkademikSeeder::class,
            MisnuSubjectSeeder::class,
            MisnuClassGroupSeeder::class,
            MisnuCalendarSeeder::class,
            PenugasanSeeder::class,
            SiswaSeeder::class,
            JadwalSeeder::class,
            JurnalSeeder::class,
            TuitionSeeder::class,
            BeritaSeeder::class,
            KesiswaanSeeder::class,
            GaleriSeeder::class,
            EkstrakurikulerSeeder::class,
            MisnuExtracurricularSeeder::class,
            UserRoleSeeder::class,
            KonselingSeeder::class,
            HomeroomSeeder::class,
            MisnuHomeroomSeeder::class,
            InventorySeeder::class,
            LibrarySeeder::class,
            LetterCategorySeeder::class,
            LetterSeeder::class,
            PpdbDemoSeeder::class,
            MutasiDemoSeeder::class,
            ScoreComponentSeeder::class,
            PpiExamSeeder::class,
        ]);
    }
}
