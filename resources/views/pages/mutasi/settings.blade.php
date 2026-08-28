<x-layouts.page
    :title="'Pengaturan Mutasi Masuk'"
    :roleLabel="$roleLabel"
    :breadcrumb="$breadcrumb"
    active-route="mutasi.settings">

    <div class="mx-auto max-w-4xl">
        <div>
            <h1 class="text-2xl font-extrabold tracking-tight text-ink sm:text-3xl">Pengaturan Mutasi Masuk</h1>
            <p class="mt-1.5 max-w-prose text-sm leading-relaxed text-ink-soft">
                Atur saklar buka/tutup pendaftaran siswa pindahan dan konten halaman informasi /pindahan untuk publik.
            </p>
        </div>

        @if (session('status'))
            <div class="mt-6"><x-ui.alert variant="success" dismissible>{{ session('status') }}</x-ui.alert></div>
        @endif
        @if ($errors->any())
            <div class="mt-6"><x-ui.alert variant="danger" dismissible>{{ $errors->first() }}</x-ui.alert></div>
        @endif

        @include('pages.mutasi.partials.steps', [
            'active' => 'mutasi.settings',
            'note' => 'Atur "Buka / Tutup Pendaftaran" di bawah. Saat tutup, /pindahan menampilkan landing page informasi; saat buka, menampilkan form pendaftaran.',
        ])

        <form method="POST" action="{{ route('mutasi.settings.update') }}" class="mt-6 space-y-6">
            @csrf
            @method('PUT')

            <x-ui.sheet title="Status Pendaftaran" subtitle="Kontrol utama membuka / menutup form publik" pinned ruled>
                <div class="flex flex-wrap items-center gap-4 rounded-[var(--radius-control)] bg-paper-deep/60 p-4 ring-1 ring-inset ring-rule">
                    <div class="min-w-[220px] flex-1">
                        <x-ui.field label="Status Pendaftaran" :error="$errors->first('mutasi_status')" required
                            hint="Buka = form tampil di /pindahan. Tutup = landing page informasi tampil.">
                            <x-ui.select name="mutasi_status" :full="true"
                                :options="['closed' => 'Tutup — tampilkan landing page informasi', 'open' => 'Buka — tampilkan form pendaftaran']"
                                :selected="old('mutasi_status', $settings->get('mutasi_status', 'closed'))" />
                        </x-ui.field>
                    </div>
                    <div class="text-sm font-bold">
                        @if (($settings->get('mutasi_status', 'closed')) === 'open')
                            <x-ui.badge variant="success">Sedang Dibuka</x-ui.badge>
                        @else
                            <x-ui.badge variant="neutral">Sedang Tutup</x-ui.badge>
                        @endif
                    </div>
                </div>
            </x-ui.sheet>

            <x-ui.sheet title="Jadwal & Timeline" subtitle="Tanggal penting yang tampil di landing page" pinned ruled>
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    @foreach ([
                        ['mutasi_tanggal_buka', 'Tanggal Dibuka'],
                        ['mutasi_tanggal_tutup', 'Tanggal Ditutup'],
                        ['mutasi_tanggal_pengumuman', 'Tanggal Pengumuman'],
                        ['mutasi_tanggal_daftar_ulang', 'Tanggal Daftar Ulang'],
                    ] as [$key, $label])
                        <x-ui.field :label="$label" :error="$errors->first($key)">
                            <input type="date" name="{{ $key }}" value="{{ old($key, $settings->get($key, '')) }}"
                                class="w-full rounded-[var(--radius-control)] bg-sheet px-3.5 py-2.5 text-sm text-ink ring-1 ring-inset ring-rule-strong transition focus:outline-none focus:ring-2 focus:ring-primary">
                        </x-ui.field>
                    @endforeach
                </div>
            </x-ui.sheet>

            <x-ui.sheet title="Syarat & Ketentuan" subtitle="Baris pertama wajib memuat Surat Rekomendasi Madrasah; kelas yang menerima pindahan" pinned ruled>
                <div class="mt-2">
                    <x-ui.field label="Syarat & Dokumen" hint="Satu item per baris" :error="$errors->first('mutasi_syarat')">
                        <textarea name="mutasi_syarat" rows="7"
                            class="w-full rounded-[var(--radius-control)] bg-sheet px-3.5 py-2.5 text-sm text-ink placeholder:text-ink-faint ring-1 ring-inset ring-rule-strong transition duration-150 focus:outline-none focus:ring-2 focus:ring-primary">{{ old('mutasi_syarat', $settings->get('mutasi_syarat', '')) }}</textarea>
                    </x-ui.field>
                </div>
                <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <x-ui.field label="Kelas yang Menerima Pindahan" hint="Satu kelas per baris (I-A, II-B, dst.)" :error="$errors->first('mutasi_kelas_tersedia')">
                        <textarea name="mutasi_kelas_tersedia" rows="4"
                            class="w-full rounded-[var(--radius-control)] bg-sheet px-3.5 py-2.5 text-sm text-ink placeholder:text-ink-faint ring-1 ring-inset ring-rule-strong transition duration-150 focus:outline-none focus:ring-2 focus:ring-primary">{{ old('mutasi_kelas_tersedia', $settings->get('mutasi_kelas_tersedia', '')) }}</textarea>
                    </x-ui.field>
                    <x-ui.field label="Kuota" :error="$errors->first('mutasi_kuota')">
                        <x-ui.input type="number" name="mutasi_kuota" min="0" placeholder="10"
                            :value="old('mutasi_kuota', $settings->get('mutasi_kuota', ''))" />
                    </x-ui.field>
                </div>
            </x-ui.sheet>

            <x-ui.sheet title="Biaya" pinned ruled>
                <x-ui.field label="Informasi Biaya" :error="$errors->first('mutasi_biaya')">
                    <textarea name="mutasi_biaya" rows="2"
                        class="w-full rounded-[var(--radius-control)] bg-sheet px-3.5 py-2.5 text-sm text-ink placeholder:text-ink-faint ring-1 ring-inset ring-rule-strong transition duration-150 focus:outline-none focus:ring-2 focus:ring-primary">{{ old('mutasi_biaya', $settings->get('mutasi_biaya', '')) }}</textarea>
                </x-ui.field>
            </x-ui.sheet>

            <x-ui.sheet title="Kontak & Bantuan" subtitle="Kontak panitia mutasi untuk publik" pinned ruled>
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <x-ui.field label="Nomor WhatsApp Panitia" :error="$errors->first('mutasi_kontak_wa')">
                        <x-ui.input name="mutasi_kontak_wa" placeholder="0812-3456-7890" :value="old('mutasi_kontak_wa', $settings->get('mutasi_kontak_wa', ''))" />
                    </x-ui.field>
                    <x-ui.field label="Nomor Telepon Panitia" :error="$errors->first('mutasi_kontak_telepon')">
                        <x-ui.input name="mutasi_kontak_telepon" placeholder="(0361) 123456" :value="old('mutasi_kontak_telepon', $settings->get('mutasi_kontak_telepon', ''))" />
                    </x-ui.field>
                    <div class="sm:col-span-2">
                        <x-ui.field label="Jam Layanan" :error="$errors->first('mutasi_jam_layanan')">
                            <x-ui.input name="mutasi_jam_layanan" placeholder="Senin–Jumat, 08.00–14.00 WIB" :value="old('mutasi_jam_layanan', $settings->get('mutasi_jam_layanan', ''))" />
                        </x-ui.field>
                    </div>
                </div>
            </x-ui.sheet>

            <x-ui.sheet title="FAQ" subtitle="Pertanyaan sering diajukan (tampil di landing page)" pinned ruled>
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

            <div class="flex justify-end gap-3">
                <x-ui.button type="submit" variant="primary" size="lg" icon="check-circle">Simpan Pengaturan</x-ui.button>
            </div>
        </form>

        {{-- Minat --}}
        <div class="mt-8">
            <x-ui.sheet title="Minat Pendaftaran Pindah" subtitle="Orang tua yang meninggalkan nama & WA saat pendaftaran masih tutup" pinned ruled>
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
                                    <form method="POST" action="{{ route('mutasi.settings.interest.destroy', $interest) }}"
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