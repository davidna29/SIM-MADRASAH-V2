<x-layouts.page
    :title="'Tambah Siswa'"
    :roleLabel="$roleLabel"
    :breadcrumb="$breadcrumb"
    active-route="siswa.create">

    <div class="mx-auto max-w-3xl">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <h1 class="text-2xl font-extrabold tracking-tight text-ink sm:text-3xl">Tambah Siswa Baru</h1>
                <p class="mt-1.5 max-w-prose text-sm leading-relaxed text-ink-soft">
                    Lembar pendaftaran siswa baru — data inti dipakai lintas modul tanpa entri ulang.
                </p>
            </div>
        </div>

        <div class="mt-6 space-y-6">
            <x-ui.form-section title="Data Inti" description="Identitas dasar siswa. NIK, nama, dan tanggal lahir sesuai akta.">
                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                    <x-ui.field label="Nama Lengkap" required>
                        <x-ui.input name="nama" placeholder="Nama sesuai akta" value="Aisyah Nur Azizah" />
                    </x-ui.field>
                    <x-ui.field label="NIS / NISN" required>
                        <x-ui.input name="nis" placeholder="Masukkan NIS" value="240106" />
                    </x-ui.field>
                    <x-ui.field label="Tempat, Tanggal Lahir" required>
                        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                            <x-ui.input name="tmpt_lahir" placeholder="Tempat lahir" value="Banyuwangi" />
                            <x-ui.input name="tgl_lahir" type="date" value="2013-05-12" />
                        </div>
                    </x-ui.field>
                    <x-ui.field label="Jenis Kelamin" required>
                        <x-ui.select name="jk" :options="['L' => 'Laki-laki', 'P' => 'Perempuan']" />
                    </x-ui.field>
                    <x-ui.field label="Agama">
                        <x-ui.select name="agama" :options="['islam' => 'Islam']" />
                    </x-ui.field>
                    <x-ui.field label="Nomor HP Orang Tua" hint="Untuk notifikasi kehadiran dan tagihan.">
                        <x-ui.input name="hp" prefix="+62" placeholder="812-3456-7890" value="81234567890" />
                    </x-ui.field>
                </div>
            </x-ui.form-section>

            <x-ui.form-section title="Penempatan Awal" description="Kelas dan tahun ajaran di mana siswa mulai bersekolah di madrasah ini.">
                <div class="grid grid-cols-1 gap-5 sm:grid-cols-3">
                    <x-ui.field label="Tahun Ajaran" required>
                        <x-ui.select name="tahun_ajaran" :options="[2026 => '2026/2027', 2025 => '2025/2026']" />
                    </x-ui.field>
                    <x-ui.field label="Tingkat">
                        <x-ui.select name="tingkat" :options="[7 => 'Kelas VII', 8 => 'Kelas VIII', 9 => 'Kelas IX']" />
                    </x-ui.field>
                    <x-ui.field label="Kelas">
                        <x-ui.select name="kelas" :options="['VII-A' => 'VII-A', 'VII-B' => 'VII-B', 'VII-C' => 'VII-C']" />
                    </x-ui.field>
                </div>
            </x-ui.form-section>

            <div class="flex flex-col-reverse items-center justify-between gap-3 border-t border-rule-strong/60 pt-5 sm:flex-row">
                <x-ui.button variant="ghost" icon="arrow-left" href="{{ route('siswa.index') }}">Kembali ke Data Siswa</x-ui.button>
                <div class="flex items-center gap-2">
                    <x-ui.button variant="secondary" icon="document-text">Simpan sebagai Draft</x-ui.button>
                    <x-ui.button variant="primary" icon="check" x-on:click="$store.toasts.push('Siswa baru berhasil disimpan dan disematkan ke papan.')">Sematkan & Simpan</x-ui.button>
                </div>
            </div>
        </div>
    </div>
</x-layouts.page>
