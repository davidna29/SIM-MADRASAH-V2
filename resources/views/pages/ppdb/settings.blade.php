<x-layouts.page
    :title="'Pengaturan PPDB'"
    :roleLabel="$roleLabel"
    :breadcrumb="$breadcrumb"
    active-route="ppdb.settings">

    <div class="mx-auto max-w-4xl">
        <div>
            <h1 class="text-2xl font-extrabold tracking-tight text-ink sm:text-3xl">Pengaturan PPDB</h1>
            <p class="mt-1.5 max-w-prose text-sm leading-relaxed text-ink-soft">
                Atur saklar buka/tutup pendaftaran dan konten halaman informasi PPDB untuk publik.
            </p>
        </div>

        @if (session('status'))
            <div class="mt-6">
                <x-ui.alert variant="success" dismissible>{{ session('status') }}</x-ui.alert>
            </div>
        @endif

        @if ($errors->any())
            <div class="mt-6">
                <x-ui.alert variant="danger" dismissible>{{ $errors->first() }}</x-ui.alert>
            </div>
        @endif

        @include('pages.ppdb.partials.steps', [
            'active' => 'ppdb.settings',
            'note' => 'Atur "Buka / Tutup Pendaftaran" di bawah. Saat tutup, halaman /ppdb menampilkan landing page informasi; saat buka, menampilkan form pendaftaran.',
        ])

        <form method="POST" action="{{ route('ppdb.settings.update') }}" class="mt-6 space-y-6">
            @csrf
            @method('PUT')

            {{-- Saklar Buka / Tutup --}}
            <x-ui.sheet title="Status Pendaftaran" subtitle="Kontrol utama membuka / menutup form pendaftaran publik" pinned ruled>
                <div class="flex flex-wrap items-center gap-4 rounded-[var(--radius-control)] bg-paper-deep/60 p-4 ring-1 ring-inset ring-rule">
                    <div class="flex-1 min-w-[220px]">
                        <x-ui.field label="Status Pendaftaran" :error="$errors->first('ppdb_status')" required
                            hint="Buka = form pendaftaran tampil di /ppdb. Tutup = landing page informasi tampil.">
                            <x-ui.select name="ppdb_status" :full="true"
                                :options="['closed' => 'Tutup — tampilkan landing page informasi', 'open' => 'Buka — tampilkan form pendaftaran']"
                                :selected="old('ppdb_status', $settings->get('ppdb_status', 'closed'))" />
                        </x-ui.field>
                    </div>
                    <div class="text-sm font-bold">
                        @if (($settings->get('ppdb_status', 'closed')) === 'open')
                            <x-ui.badge variant="success">Sedang Dibuka</x-ui.badge>
                        @else
                            <x-ui.badge variant="neutral">Sedang Tutup</x-ui.badge>
                        @endif
                    </div>
                </div>
            </x-ui.sheet>

            {{-- Jadwal --}}
            <x-ui.sheet title="Jadwal & Timeline" subtitle="Tanggal penting yang tampil di landing page publik" pinned ruled>
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <x-ui.field label="Tanggal Dibuka" :error="$errors->first('ppdb_tanggal_buka')">
                        <input type="date" name="ppdb_tanggal_buka"
                            value="{{ old('ppdb_tanggal_buka', $settings->get('ppdb_tanggal_buka', '')) }}"
                            class="w-full rounded-[var(--radius-control)] bg-sheet px-3.5 py-2.5 text-sm text-ink ring-1 ring-inset ring-rule-strong transition focus:outline-none focus:ring-2 focus:ring-primary">
                    </x-ui.field>
                    <x-ui.field label="Tanggal Ditutup" :error="$errors->first('ppdb_tanggal_tutup')">
                        <input type="date" name="ppdb_tanggal_tutup"
                            value="{{ old('ppdb_tanggal_tutup', $settings->get('ppdb_tanggal_tutup', '')) }}"
                            class="w-full rounded-[var(--radius-control)] bg-sheet px-3.5 py-2.5 text-sm text-ink ring-1 ring-inset ring-rule-strong transition focus:outline-none focus:ring-2 focus:ring-primary">
                    </x-ui.field>
                    <x-ui.field label="Tanggal Pengumuman" :error="$errors->first('ppdb_tanggal_pengumuman')">
                        <input type="date" name="ppdb_tanggal_pengumuman"
                            value="{{ old('ppdb_tanggal_pengumuman', $settings->get('ppdb_tanggal_pengumuman', '')) }}"
                            class="w-full rounded-[var(--radius-control)] bg-sheet px-3.5 py-2.5 text-sm text-ink ring-1 ring-inset ring-rule-strong transition focus:outline-none focus:ring-2 focus:ring-primary">
                    </x-ui.field>
                    <x-ui.field label="Tanggal Daftar Ulang" :error="$errors->first('ppdb_tanggal_daftar_ulang')">
                        <input type="date" name="ppdb_tanggal_daftar_ulang"
                            value="{{ old('ppdb_tanggal_daftar_ulang', $settings->get('ppdb_tanggal_daftar_ulang', '')) }}"
                            class="w-full rounded-[var(--radius-control)] bg-sheet px-3.5 py-2.5 text-sm text-ink ring-1 ring-inset ring-rule-strong transition focus:outline-none focus:ring-2 focus:ring-primary">
                    </x-ui.field>
                </div>
            </x-ui.sheet>

            {{-- Syarat & Ketentuan --}}
            <x-ui.sheet title="Syarat & Ketentuan" subtitle="Usia minimal, dokumen wajib, dan kuota yang ditampilkan" pinned ruled>
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <x-ui.field label="Usia Minimal" hint="Umur per 1 Juli tahun berjalan (mis. 6)" :error="$errors->first('ppdb_usia_min')">
                        <x-ui.input type="number" name="ppdb_usia_min" min="0" max="20"
                            :value="old('ppdb_usia_min', $settings->get('ppdb_usia_min', '6'))" placeholder="6" />
                    </x-ui.field>
                    <x-ui.field label="Keterangan Usia" :error="$errors->first('ppdb_usia_ket')">
                        <x-ui.input name="ppdb_usia_ket" :value="old('ppdb_usia_ket', $settings->get('ppdb_usia_ket', ''))" placeholder="per 1 Juli tahun berjalan" />
                    </x-ui.field>
                    <x-ui.field label="Kuota / Daya Tampung per Kelas" :error="$errors->first('ppdb_kuota')">
                        <x-ui.input type="number" name="ppdb_kuota" min="0"
                            :value="old('ppdb_kuota', $settings->get('ppdb_kuota', '28'))" placeholder="28" />
                    </x-ui.field>
                </div>
                <div class="mt-4">
                    <x-ui.field label="Dokumen yang Wajib Disiapkan" hint="Satu item per baris" :error="$errors->first('ppdb_dokumen')">
                        <textarea name="ppdb_dokumen" rows="6"
                            class="w-full rounded-[var(--radius-control)] bg-sheet px-3.5 py-2.5 text-sm text-ink placeholder:text-ink-faint ring-1 ring-inset ring-rule-strong transition duration-150 focus:outline-none focus:ring-2 focus:ring-primary">{{ old('ppdb_dokumen', $settings->get('ppdb_dokumen', '')) }}</textarea>
                    </x-ui.field>
                </div>
            </x-ui.sheet>

            {{-- Jalur & Biaya --}}
            <x-ui.sheet title="Jalur Pendaftaran & Biaya" subtitle="Jalur yang tersedia dan informasi biaya" pinned ruled>
                <div class="mt-2">
                    <x-ui.field label="Jalur Pendaftaran" hint="Satu jalur per baris (Reguler, Prestasi, dst.)" :error="$errors->first('ppdb_jalur')">
                        <textarea name="ppdb_jalur" rows="3"
                            class="w-full rounded-[var(--radius-control)] bg-sheet px-3.5 py-2.5 text-sm text-ink placeholder:text-ink-faint ring-1 ring-inset ring-rule-strong transition duration-150 focus:outline-none focus:ring-2 focus:ring-primary">{{ old('ppdb_jalur', $settings->get('ppdb_jalur', '')) }}</textarea>
                    </x-ui.field>
                </div>
                <div class="mt-4">
                    <x-ui.field label="Informasi Biaya" :error="$errors->first('ppdb_biaya')">
                        <textarea name="ppdb_biaya" rows="2"
                            class="w-full rounded-[var(--radius-control)] bg-sheet px-3.5 py-2.5 text-sm text-ink placeholder:text-ink-faint ring-1 ring-inset ring-rule-strong transition duration-150 focus:outline-none focus:ring-2 focus:ring-primary">{{ old('ppdb_biaya', $settings->get('ppdb_biaya', '')) }}</textarea>
                    </x-ui.field>
                </div>
            </x-ui.sheet>

            {{-- Kontak & Bantuan --}}
            <x-ui.sheet title="Kontak & Bantuan" subtitle="Kontak panitia PPDB untuk publik" pinned ruled>
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <x-ui.field label="Nomor WhatsApp Panitia" :error="$errors->first('ppdb_kontak_wa')">
                        <x-ui.input name="ppdb_kontak_wa" placeholder="0812-3456-7890"
                            :value="old('ppdb_kontak_wa', $settings->get('ppdb_kontak_wa', ''))" />
                    </x-ui.field>
                    <x-ui.field label="Nomor Telepon Panitia" :error="$errors->first('ppdb_kontak_telepon')">
                        <x-ui.input name="ppdb_kontak_telepon" placeholder="(0361) 123456"
                            :value="old('ppdb_kontak_telepon', $settings->get('ppdb_kontak_telepon', ''))" />
                    </x-ui.field>
                    <div class="sm:col-span-2">
                        <x-ui.field label="Jam Layanan" :error="$errors->first('ppdb_jam_layanan')">
                            <x-ui.input name="ppdb_jam_layanan" placeholder="Senin–Jumat, 08.00–14.00 WIB"
                                :value="old('ppdb_jam_layanan', $settings->get('ppdb_jam_layanan', ''))" />
                        </x-ui.field>
                    </div>
                </div>
            </x-ui.sheet>

            {{-- FAQ --}}
            <x-ui.sheet title="FAQ" subtitle="Pertanyaan yang sering diajukan (tampil di landing page)" pinned ruled>
                <div id="faq-list" class="space-y-3">
                    @foreach ($faq as $item)
                        <div class="faq-row space-y-2 rounded-[var(--radius-control)] bg-paper-deep/50 p-3 ring-1 ring-inset ring-rule/60">
                            <div class="flex items-center justify-between gap-2">
                                <span class="text-xs font-bold text-ink">Pertanyaan</span>
                                <button type="button" class="faq-remove text-xs font-semibold text-danger hover:underline">Hapus</button>
                            </div>
                            <input name="faq_q[]" value="{{ $item['q'] }}" placeholder="Pertanyaan…"
                                class="w-full rounded-[var(--radius-control)] bg-sheet px-3.5 py-2 text-sm text-ink ring-1 ring-inset ring-rule-strong transition focus:outline-none focus:ring-2 focus:ring-primary">
                            <textarea name="faq_a[]" rows="2" placeholder="Jawaban…"
                                class="w-full rounded-[var(--radius-control)] bg-sheet px-3.5 py-2 text-sm text-ink placeholder:text-ink-faint ring-1 ring-inset ring-rule-strong transition duration-150 focus:outline-none focus:ring-2 focus:ring-primary">{{ $item['a'] }}</textarea>
                        </div>
                    @endforeach
                </div>

                <template id="faq-row-template">
                    <div class="faq-row space-y-2 rounded-[var(--radius-control)] bg-paper-deep/50 p-3 ring-1 ring-inset ring-rule/60">
                        <div class="flex items-center justify-between gap-2">
                            <span class="text-xs font-bold text-ink">Pertanyaan</span>
                            <button type="button" class="faq-remove text-xs font-semibold text-danger hover:underline">Hapus</button>
                        </div>
                        <input name="faq_q[]" placeholder="Pertanyaan…"
                            class="w-full rounded-[var(--radius-control)] bg-sheet px-3.5 py-2 text-sm text-ink ring-1 ring-inset ring-rule-strong transition focus:outline-none focus:ring-2 focus:ring-primary">
                        <textarea name="faq_a[]" rows="2" placeholder="Jawaban…"
                            class="w-full rounded-[var(--radius-control)] bg-sheet px-3.5 py-2 text-sm text-ink placeholder:text-ink-faint ring-1 ring-inset ring-rule-strong transition duration-150 focus:outline-none focus:ring-2 focus:ring-primary"></textarea>
                    </div>
                </template>

                <div class="mt-3">
                    <x-ui.button type="button" variant="secondary" size="sm" icon="plus" id="faq-add-btn">Tambah FAQ</x-ui.button>
                </div>
            </x-ui.sheet>

            {{-- Submit --}}
            <div class="flex justify-end gap-3">
                <x-ui.button type="submit" variant="primary" size="lg" icon="check-circle">Simpan Pengaturan</x-ui.button>
            </div>
        </form>

        {{-- Minat Pendaftaran --}}
        <div class="mt-8">
            <x-ui.sheet title="Minat Pendaftaran" subtitle="Orang tua yang meninggalkan nama & WA untuk dihubungi saat pendaftaran dibuka" pinned ruled>
                @if ($interests->isEmpty())
                    <div class="py-10 text-center">
                        <x-svg-user-plus class="mx-auto size-10 text-ink-faint/40" />
                        <p class="mt-3 text-sm text-ink-faint">Belum ada minat pendaftaran.</p>
                    </div>
                @else
                    <x-ui.table :headers="['Nama', 'Nomor WA', 'Tanggal', 'Aksi']">
                        @foreach ($interests as $interest)
                            <tr class="hover:bg-paper-deep/50">
                                <td class="px-4 py-3 text-sm font-bold text-ink">{{ $interest->name }}</td>
                                <td class="px-4 py-3 font-mono text-xs text-ink">{{ $interest->phone }}</td>
                                <td class="px-4 py-3 text-xs text-ink-soft">{{ $interest->created_at->format('d/m/Y H:i') }}</td>
                                <td class="px-4 py-3 text-right">
                                    <form method="POST" action="{{ route('ppdb.settings.interest.destroy', $interest) }}"
                                        onsubmit="return confirm('Hapus minat ini?')" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-xs font-semibold text-danger hover:underline">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </x-ui.table>
                    <div class="border-t border-rule/70 px-1 pt-3 pb-3">
                        <x-ui.pagination :current="$interests->currentPage()" :last="$interests->lastPage()" />
                    </div>
                @endif
            </x-ui.sheet>
        </div>
    </div>

    <script>
        document.addEventListener('click', function (e) {
            if (e.target.closest('#faq-add-btn')) {
                var template = document.getElementById('faq-row-template');
                var clone = template.content.cloneNode(true);
                document.getElementById('faq-list').appendChild(clone);
            }
            if (e.target.closest('.faq-remove')) {
                e.target.closest('.faq-row').remove();
            }
        });
    </script>
</x-layouts.page>
