<x-layouts.publik :title="'PPDB - Informasi Pendaftaran'">
    @php
        $fmt = function ($d) { return $d ? \Carbon\Carbon::parse($d)->translatedFormat('j F Y') : null; };
        $isOpen = $settings->get('ppdb_status', 'closed') === 'open';
        $buka = $settings->get('ppdb_tanggal_buka', '');
        $tutup = $settings->get('ppdb_tanggal_tutup', '');
        $pengumuman = $settings->get('ppdb_tanggal_pengumuman', '');
        $daftarUlang = $settings->get('ppdb_tanggal_daftar_ulang', '');
        $usiaMin = $settings->get('ppdb_usia_min', '');
        $usiaKet = $settings->get('ppdb_usia_ket', '');
        $kuota = $settings->get('ppdb_kuota', '');
        $dokumen = collect(explode("\n", $settings->get('ppdb_dokumen', '')))->map(fn ($x) => trim($x))->filter()->values();
        $jalur = collect(explode("\n", $settings->get('ppdb_jalur', '')))->map(fn ($x) => trim($x))->filter()->values();
        $biaya = $settings->get('ppdb_biaya', '');
        $wa = $settings->get('ppdb_kontak_wa', '');
        $telp = $settings->get('ppdb_kontak_telepon', '') ?: \App\Models\Setting::get('madrasah_phone', '');
        $jamLayanan = $settings->get('ppdb_jam_layanan', '');
        $faq = json_decode((string) $settings->get('ppdb_faq', '[]'), true);
        $faq = is_array($faq) ? $faq : [];
        $waLink = $wa ? 'https://wa.me/' . preg_replace('/\D/', '', $wa) : null;
    @endphp

    <div class="mx-auto max-w-4xl">
        {{-- Hero --}}
        <section class="text-center">
            <x-ui.badge :variant="$isOpen ? 'success' : 'neutral'" class="mb-4">
                {{ $isOpen ? 'Sedang Dibuka' : 'Pendaftaran Belum Dibuka' }}
            </x-ui.badge>
            <h1 class="text-3xl font-extrabold tracking-tight text-ink sm:text-4xl">Penerimaan Peserta Didik Baru</h1>
            @if ($tahun)
                <p class="mt-2 text-sm font-semibold text-primary">{{ $tahun->name ?? $tahun->year ?? '' }}</p>
            @endif
            <p class="mx-auto mt-3 max-w-prose text-sm leading-relaxed text-ink-soft">
                Persiapkan diri Anda dan buah hati. Di bawah ini Anda bisa melihat jadwal, syarat,
                dan alur pendaftaran. Ketika pendaftaran dibuka, formulir akan muncul di halaman ini.
            </p>
        </section>

        {{-- CTA + Pre-registrasi --}}
        <section class="mt-8">
            <div class="rounded-sheet bg-board text-board shadow-sheet-raised ring-1 ring-inset ring-rule">
                <div class="grid grid-cols-1 gap-6 p-6 sm:p-8 md:grid-cols-2">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wide text-board-ink/70">Pendaftaran dibuka</p>
                        @if ($buka)
                            <div class="mt-2" x-data="ppdbCountdown" data-target="{{ \Carbon\Carbon::parse($buka)->toIso8601String() }}" x-init="start()">
                                <template x-if="!ready">
                                    <p class="text-2xl font-extrabold text-board">{{ $fmt($buka) }}</p>
                                </template>
                                <template x-if="ready">
                                    <div>
                                        <div class="mt-3 grid max-w-sm grid-cols-4 gap-2">
                                            <div class="rounded-[var(--radius-control)] bg-board-deep/30 px-2 py-2 text-center">
                                                <p class="text-2xl font-extrabold tabular text-board" x-text="left.d"></p>
                                                <p class="text-[10px] font-semibold uppercase text-board-ink/70">Hari</p>
                                            </div>
                                            <div class="rounded-[var(--radius-control)] bg-board-deep/30 px-2 py-2 text-center">
                                                <p class="text-2xl font-extrabold tabular text-board" x-text="left.h"></p>
                                                <p class="text-[10px] font-semibold uppercase text-board-ink/70">Jam</p>
                                            </div>
                                            <div class="rounded-[var(--radius-control)] bg-board-deep/30 px-2 py-2 text-center">
                                                <p class="text-2xl font-extrabold tabular text-board" x-text="left.m"></p>
                                                <p class="text-[10px] font-semibold uppercase text-board-ink/70">Menit</p>
                                            </div>
                                            <div class="rounded-[var(--radius-control)] bg-board-deep/30 px-2 py-2 text-center">
                                                <p class="text-2xl font-extrabold tabular text-board" x-text="left.s"></p>
                                                <p class="text-[10px] font-semibold uppercase text-board-ink/70">Detik</p>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        @else
                            <p class="mt-2 text-2xl font-extrabold text-board">Segera</p>
                        @endif

                        @if ($isOpen)
                            <a href="#daftar" class="mt-6 inline-flex items-center gap-2 rounded-[var(--radius-control)] bg-success px-5 py-3 text-sm font-bold text-white shadow-sm transition hover:brightness-95">
                                <x-svg-check-circle class="size-4" /> Daftar Sekarang
                            </a>
                        @else
                            <button type="button" disabled
                                class="mt-6 inline-flex cursor-not-allowed items-center gap-2 rounded-[var(--radius-control)] bg-board-ink/20 px-5 py-3 text-sm font-bold text-board-ink/60">
                                <x-svg-lock-closed class="size-4" /> Form Belum Dibuka
                            </button>
                        @endif
                    </div>

                    <div class="md:border-l md:border-board-deep/20 md:pl-6">
                        @if (session('status'))
                            <div class="mb-4 rounded-[var(--radius-control)] bg-success/90 px-4 py-3 text-xs font-semibold text-white">
                                {{ session('status') }}
                            </div>
                        @endif
                        <p class="text-sm font-bold text-board">Daftar Info via WhatsApp</p>
                        <p class="mt-1 text-xs leading-relaxed text-board-ink/70">
                            Tinggalkan nama & nomor WhatsApp Anda. Panitia akan menghubungi saat pendaftaran resmi dibuka.
                        </p>
                        <form method="POST" action="{{ route('ppdb.interest.store') }}" class="mt-4 space-y-3">
                            @csrf
                            <div>
                                <input name="name" value="{{ old('name') }}" required maxlength="100" placeholder="Nama orang tua / wali"
                                    class="w-full rounded-[var(--radius-control)] bg-board px-3.5 py-2.5 text-sm text-board-ink placeholder:text-board-ink/50 ring-1 ring-inset ring-board-deep/30 focus:outline-none focus:ring-2 focus:ring-primary">
                                @error('name')<p class="mt-1 text-xs font-medium text-danger">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <input name="phone" value="{{ old('phone') }}" required maxlength="20" placeholder="Nomor WhatsApp"
                                    class="w-full rounded-[var(--radius-control)] bg-board px-3.5 py-2.5 text-sm text-board-ink placeholder:text-board-ink/50 ring-1 ring-inset ring-board-deep/30 focus:outline-none focus:ring-2 focus:ring-primary">
                                @error('phone')<p class="mt-1 text-xs font-medium text-danger">{{ $message }}</p>@enderror
                            </div>
                            <button type="submit"
                                class="inline-flex w-full items-center justify-center gap-2 rounded-[var(--radius-control)] bg-board-ink px-4 py-2.5 text-sm font-bold text-board transition hover:opacity-90">
                                <x-svg-bell class="size-4" /> Ingatkan Saya
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </section>

        {{-- Timeline --}}
        @if ($buka || $tutup || $pengumuman || $daftarUlang)
            <section class="mt-10">
                <h2 class="flex items-center gap-2 text-lg font-extrabold tracking-tight text-ink">
                    <x-svg-calendar-days class="size-5 text-primary" /> Jadwal Penting
                </h2>
                <div class="mt-4 grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-4">
                    @foreach ([
                        ['label' => 'Pendaftaran Dibuka', 'date' => $buka, 'icon' => 'play'],
                        ['label' => 'Pendaftaran Ditutup', 'date' => $tutup, 'icon' => 'stop'],
                        ['label' => 'Pengumuman', 'date' => $pengumuman, 'icon' => 'megaphone'],
                        ['label' => 'Daftar Ulang', 'date' => $daftarUlang, 'icon' => 'clipboard-document-check'],
                    ] as $item)
                        <div class="rounded-sheet bg-sheet p-4 shadow-sheet ring-1 ring-inset ring-rule/60">
                            <x-dynamic-component :component="'svg-' . $item['icon']" class="size-5 text-primary" aria-hidden="true" />
                            <p class="mt-2 text-xs font-semibold text-ink-soft">{{ $item['label'] }}</p>
                            <p class="mt-0.5 text-sm font-extrabold text-ink">{{ $fmt($item['date']) ?? 'Segera' }}</p>
                        </div>
                    @endforeach
                </div>
            </section>
        @endif

        {{-- Alur --}}
        <section class="mt-10">
            <h2 class="flex items-center gap-2 text-lg font-extrabold tracking-tight text-ink">
                <x-svg-queue-list class="size-5 text-primary" /> Alur Pendaftaran
            </h2>
            <div class="mt-4 grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ([
                    ['step' => '1', 'title' => 'Isi Data', 'desc' => 'Lengkapi formulir pendaftaran online.', 'icon' => 'pencil-square'],
                    ['step' => '2', 'title' => 'Upload Berkas', 'desc' => 'Siapkan scan dokumen yang dibutuhkan.', 'icon' => 'document-arrow-up'],
                    ['step' => '3', 'title' => 'Verifikasi', 'desc' => 'Panitia memeriksa kelengkapan data.', 'icon' => 'check-badge'],
                    ['step' => '4', 'title' => 'Pengumuman', 'desc' => 'Hasil seleksi diumumkan sesuai jadwal.', 'icon' => 'flag'],
                ] as $i => $item)
                    <div class="relative rounded-sheet bg-sheet p-4 shadow-sheet ring-1 ring-inset ring-rule/60">
                        <span class="flex size-9 items-center justify-center rounded-full bg-primary text-sm font-extrabold text-white">{{ $item['step'] }}</span>
                        <p class="mt-3 text-sm font-extrabold text-ink">{{ $item['title'] }}</p>
                        <p class="mt-1 text-xs leading-relaxed text-ink-soft">{{ $item['desc'] }}</p>
                    </div>
                @endforeach
            </div>
        </section>

        {{-- Syarat --}}
        <section class="mt-10">
            <h2 class="flex items-center gap-2 text-lg font-extrabold tracking-tight text-ink">
                <x-svg-document-text class="size-5 text-primary" /> Syarat & Ketentuan
            </h2>
            <div class="mt-4 grid grid-cols-1 gap-3 md:grid-cols-3">
                <div class="rounded-sheet bg-sheet p-4 shadow-sheet ring-1 ring-inset ring-rule/60">
                    <p class="flex items-center gap-1.5 text-xs font-semibold text-ink-soft"><x-svg-cake class="size-4 text-primary" /> Usia Minimal</p>
                    <p class="mt-1.5 text-sm font-extrabold text-ink">{{ $usiaMin ? $usiaMin . ' tahun' : '—' }} <span class="font-medium text-ink-soft">{{ $usiaKet }}</span></p>
                </div>
                @if ($kuota !== '')
                    <div class="rounded-sheet bg-sheet p-4 shadow-sheet ring-1 ring-inset ring-rule/60">
                        <p class="flex items-center gap-1.5 text-xs font-semibold text-ink-soft"><x-svg-user-group class="size-4 text-primary" /> Kuota per Kelas</p>
                        <p class="mt-1.5 text-sm font-extrabold text-ink">{{ $kuota }} siswa</p>
                    </div>
                @endif
                <div class="rounded-sheet bg-sheet p-4 shadow-sheet ring-1 ring-inset ring-rule/60 md:col-span-1">
                    <p class="flex items-center gap-1.5 text-xs font-semibold text-ink-soft"><x-svg-folder class="size-4 text-primary" /> Dokumen Wajib</p>
                    <ul class="mt-2 space-y-1.5">
                        @forelse ($dokumen as $doc)
                            <li class="flex items-start gap-1.5 text-xs leading-relaxed text-ink">
                                <x-svg-check class="mt-0.5 size-3.5 shrink-0 text-success" /> {{ $doc }}
                            </li>
                        @empty
                            <li class="text-xs text-ink-faint">Dokumen diumumkan kemudian.</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </section>

        {{-- Jalur & Biaya --}}
        @if ($jalur->isNotEmpty() || $biaya !== '')
            <section class="mt-10">
                <h2 class="flex items-center gap-2 text-lg font-extrabold tracking-tight text-ink">
                    <x-svg-user-plus class="size-5 text-primary" /> Jalur Pendaftaran & Biaya
                </h2>
                <div class="mt-4 grid grid-cols-1 gap-3 md:grid-cols-2">
                    @if ($jalur->isNotEmpty())
                        <div class="rounded-sheet bg-sheet p-4 shadow-sheet ring-1 ring-inset ring-rule/60">
                            <p class="text-xs font-semibold text-ink-soft">Jalur Pendaftaran</p>
                            <ul class="mt-2 space-y-1.5">
                                @foreach ($jalur as $j)
                                    <li class="flex items-start gap-1.5 text-sm text-ink"><x-svg-check-badge class="mt-0.5 size-4 shrink-0 text-primary" /> {{ $j }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    @if ($biaya !== '')
                        <div class="rounded-sheet bg-sheet p-4 shadow-sheet ring-1 ring-inset ring-rule/60">
                            <p class="flex items-center gap-1.5 text-xs font-semibold text-ink-soft"><x-svg-banknotes class="size-4 text-primary" /> Biaya Pendaftaran</p>
                            <p class="mt-2 text-sm font-bold text-ink">{{ $biaya }}</p>
                        </div>
                    @endif
                </div>
            </section>
        @endif

        {{-- Kontak --}}
        @if ($wa || $telp || $jamLayanan)
            <section class="mt-10">
                <h2 class="flex items-center gap-2 text-lg font-extrabold tracking-tight text-ink">
                    <x-svg-lifebuoy class="size-5 text-primary" /> Kontak & Bantuan
                </h2>
                <div class="mt-4 grid grid-cols-1 gap-3 sm:grid-cols-3">
                    @if ($wa)
                        <a href="{{ $waLink }}" target="_blank" rel="noopener" class="rounded-sheet bg-sheet p-4 shadow-sheet ring-1 ring-inset ring-rule/60 transition hover:ring-primary/40">
                            <p class="flex items-center gap-1.5 text-xs font-semibold text-ink-soft"><x-svg-chat-bubble-oval-left-ellipsis class="size-4 text-primary" /> WhatsApp</p>
                            <p class="mt-1.5 text-sm font-extrabold text-ink">{{ $wa }}</p>
                        </a>
                    @endif
                    @if ($telp)
                        <div class="rounded-sheet bg-sheet p-4 shadow-sheet ring-1 ring-inset ring-rule/60">
                            <p class="flex items-center gap-1.5 text-xs font-semibold text-ink-soft"><x-svg-phone class="size-4 text-primary" /> Telepon</p>
                            <p class="mt-1.5 text-sm font-extrabold text-ink">{{ $telp }}</p>
                        </div>
                    @endif
                    @if ($jamLayanan)
                        <div class="rounded-sheet bg-sheet p-4 shadow-sheet ring-1 ring-inset ring-rule/60">
                            <p class="flex items-center gap-1.5 text-xs font-semibold text-ink-soft"><x-svg-clock class="size-4 text-primary" /> Jam Layanan</p>
                            <p class="mt-1.5 text-sm font-extrabold text-ink">{{ $jamLayanan }}</p>
                        </div>
                    @endif
                </div>
            </section>
        @endif

        {{-- FAQ --}}
        @if ($faq)
            <section class="mt-10" id="daftar">
                <h2 class="flex items-center gap-2 text-lg font-extrabold tracking-tight text-ink">
                    <x-svg-question-mark-circle class="size-5 text-primary" /> Pertanyaan yang Sering Diajukan
                </h2>
                <div class="mt-4 space-y-2" x-data="{ open: null }">
                    @foreach ($faq as $i => $item)
                        <div class="overflow-hidden rounded-sheet bg-sheet shadow-sheet ring-1 ring-inset ring-rule/60">
                            <button type="button" @click="open = open === {{ $i }} ? null : {{ $i }}"
                                class="flex w-full items-center justify-between gap-3 px-4 py-3 text-left">
                                <span class="text-sm font-bold text-ink">{{ $item['q'] ?? '' }}</span>
                                <x-svg-chevron-down class="size-4 shrink-0 text-ink-soft transition duration-200" x-bind:class="open === {{ $i }} ? 'rotate-180' : ''" />
                            </button>
                            <div x-show="open === {{ $i }}" x-cloak x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 -translate-y-1"
                                x-transition:enter-end="opacity-100 translate-y-0">
                                <p class="px-4 pb-4 text-sm leading-relaxed text-ink-soft">{{ $item['a'] ?? '' }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>
        @endif
    </div>

    <script>
        function ppdbCountdown() {
            return {
                ready: false,
                target: null,
                targetLabel: '',
                left: { d: 0, h: 0, m: 0, s: 0 },
                start() {
                    var t = this.$el.getAttribute('data-target');
                    if (!t) { this.ready = false; return; }
                    this.target = new Date(t).getTime();
                    this.ready = !isNaN(this.target);
                    var self = this;
                    var tick = function () {
                        var diff = self.target - Date.now();
                        if (diff <= 0) { self.left = { d: 0, h: 0, m: 0, s: 0 }; return; }
                        self.left = {
                            d: Math.floor(diff / 86400000),
                            h: Math.floor(diff / 3600000) % 24,
                            m: Math.floor(diff / 60000) % 60,
                            s: Math.floor(diff / 1000) % 60
                        };
                    };
                    tick();
                    setInterval(tick, 1000);
                }
            };
        }
    </script>
</x-layouts.publik>
