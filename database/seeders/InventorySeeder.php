<?php

namespace Database\Seeders;

use App\Models\InventoryCategory;
use App\Models\InventoryItem;
use App\Models\InventoryMaintenance;
use App\Models\InventoryMutation;
use App\Models\User;
use Illuminate\Database\Seeder;

class InventorySeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('username', 'admin')->first();
        $userId = $admin?->id;

        $kategori = [
            'Elektronik' => 'Perangkat listrik & elektronik (LCD, AC, komputer).',
            'Furniture' => 'Meja, kursi, lemari, dan perabotan.',
            'Alat Tulis Kantor' => 'ATK dan perlengkapan administrasi.',
            'Olahraga' => 'Perlengkapan olahraga & sarana kegiatan fisik.',
            'Lab & Peraga' => 'Alat laboratorium dan alat peraga pembelajaran.',
            'Perpustakaan' => 'Koleksi buku dan sarana perpustakaan.',
        ];

        foreach ($kategori as $name => $desc) {
            InventoryCategory::firstOrCreate(['name' => $name], ['description' => $desc]);
        }

        $items = [
            ['Proyektor LCD', 'Elektronik', 'Epson', 'EB-X05', 'EKS-001', 4, 'unit', 'baik', 'Ruang Aula', 25000000],
            ['AC Split 2 PK', 'Elektronik', 'Daikin', 'FTC50', 'AC-001', 6, 'unit', 'baik', 'Ruang Kelas I-A', 90000000],
            ['Meja Belajar Siswa', 'Furniture', null, null, null, 120, 'unit', 'baik', 'Gudang', 36000000],
            ['Kursi Tamu', 'Furniture', 'Olympic', 'Sofa', 'KRS-001', 2, 'set', 'rusak_ringan', 'Ruang Tamu', 4500000],
            ['Kertas HVS A4', 'Alat Tulis Kantor', 'Sidu', '70 gsm', null, 50, 'rim', 'baik', 'Gudang ATK', 2750000],
            ['Bola Sepak', 'Olahraga', 'Adidas', 'Club', 'BOL-001', 10, 'buah', 'baik', 'Gudang Olahraga', 1000000],
            ['Mikroskop', 'Lab & Peraga', 'Olympus', 'CX23', 'MIC-001', 2, 'unit', 'baik', 'Lab IPA', 18000000],
            ['Rak Buku', 'Perpustakaan', null, null, null, 8, 'unit', 'baik', 'Perpustakaan', 9600000],
            ['Komputer Guru', 'Elektronik', 'Dell', 'OptiPlex', 'PC-001', 15, 'unit', 'rusak_berat', 'Ruang Guru', 15000000],
        ];

        $barang = [];
        foreach ($items as $idx => [$name, $cat, $brand, $model, $serial, $qty, $unit, $condition, $location, $price]) {
            $barang[] = InventoryItem::firstOrCreate(
                ['code' => 'INV-'.now()->format('Ym').'-'.str_pad((string) ($idx + 1), 3, '0', STR_PAD_LEFT)],
                [
                    'category_id' => InventoryCategory::where('name', $cat)->first()->id,
                    'name' => $name,
                    'brand' => $brand,
                    'model' => $model,
                    'serial_number' => $serial,
                    'quantity' => $qty,
                    'unit' => $unit,
                    'condition' => $condition,
                    'location' => $location,
                    'purchase_date' => now()->subYear()->toDateString(),
                    'purchase_price' => $price,
                    'status' => $condition === 'rusak_berat' ? 'tidak_aktif' : 'tersedia',
                    'created_by' => $userId,
                ]
            );
        }

        // Contoh mutasi disetujui: proyektor dipindah ke Ruang Guru
        if (isset($barang[0])) {
            InventoryMutation::firstOrCreate(
                [
                    'item_id' => $barang[0]->id,
                    'to_location' => 'Ruang Guru',
                    'mutation_date' => now()->subMonths(2)->toDateString(),
                ],
                [
                    'from_location' => 'Ruang Aula',
                    'quantity' => 1,
                    'reason' => 'Kebutuhan pengajaran',
                    'status' => 'disetujui',
                    'approved_by' => $userId,
                    'created_by' => $userId,
                ]
            );
            $barang[0]->update(['location' => 'Ruang Guru']);
        }

        // Contoh pemeliharaan selesai pada AC
        if (isset($barang[1])) {
            InventoryMaintenance::firstOrCreate(
                [
                    'item_id' => $barang[1]->id,
                    'type' => 'perawatan',
                    'start_date' => now()->subMonth()->toDateString(),
                ],
                [
                    'description' => 'Servis berkala AC ruang kelas.',
                    'cost' => 500000,
                    'end_date' => now()->subWeeks(3)->toDateString(),
                    'technician' => 'Toko Elektronik Jaya',
                    'status' => 'selesai',
                    'created_by' => $userId,
                ]
            );
        }

        // Contoh pemeliharaan berlangsung pada komputer rusak berat
        if (isset($barang[8])) {
            InventoryMaintenance::firstOrCreate(
                [
                    'item_id' => $barang[8]->id,
                    'type' => 'perbaikan',
                    'start_date' => now()->subWeek()->toDateString(),
                ],
                [
                    'description' => 'Perbaikan motherboard komputer guru.',
                    'cost' => 1200000,
                    'technician' => 'Toko Komputer Andalan',
                    'status' => 'berlangsung',
                    'created_by' => $userId,
                ]
            );
        }
    }
}
