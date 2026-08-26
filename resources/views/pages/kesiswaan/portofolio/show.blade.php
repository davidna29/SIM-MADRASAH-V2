@php
    $p = $portofolio;
@endphp

<x-layouts.page title="Portofolio — {{ $student->name }}" :roleLabel="$roleLabel" :breadcrumb="$breadcrumb">
    {{-- Aksi --}}
    <x-slot:actions>
        <x-ui.button variant="secondary" icon="qr-code" href="{{ route('portofolio.qr', $student) }}" target="_blank">QR Code</x-ui.button>
        <x-ui.button variant="secondary" icon="printer" href="{{ route('portofolio.print', $student) }}">Cetak PDF</x-ui.button>
    </x-slot:actions>

    <div class="mx-auto max-w-6xl space-y-6">
        {{-- Header Siswa --}}
        <x-ui.sheet :pinned="true">
            <div class="flex flex-wrap items-center gap-5">
                <span class="flex size-16 shrink-0 items-center justify-center rounded-full bg-primary-soft text-xl font-bold text-primary-strong">
                    {{ mb_strtoupper(mb_substr($student->name, 0, 2)) }}
                </span>
                <div class="min-w-0 flex-1">
                    <h1 class="text-lg font-bold tracking-tight text-ink sm:text-xl">{{ $student->name }}</h1>
                    <div class="mt-1 flex flex-wrap items-center gap-2 text-sm text-ink-soft">
                        <span>NIS: <strong class="font-mono text-ink">{{ $student->nis }}</strong></span>
                        <span class="text-rule-strong">·</span>
                        <span>Kelas: <strong class="text-ink">{{ $p['kelas'] }}</strong></span>
                        <span class="text-rule-strong">·</span>
                        <span>{{ $p['report']?->academicYear?->name ?? '–' }} · {{ ucfirst($p['report']?->semester ?? '–') }}</span>
                    </div>
                </div>
            </div>
        </x-ui.sheet>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            {{-- Kolom Kiri (2/3) --}}
            <div class="space-y-6 lg:col-span-2">
                {{-- Rapor --}}
                <x-ui.sheet title="Nilai / Rapor" subtitle="Rekap nilai per mata pelajaran" :pinned="true" :padding="false">
                    @if ($p['raporItems']->isEmpty())
                        <div class="px-6 py-8 text-center text-sm text-ink-soft">Belum ada rapor terbit untuk semester ini.</div>
                    @else
                        <x-ui.table :headers="['Mata Pelajaran', 'Nilai', 'Predikat', 'Deskripsi']">
                            @foreach ($p['raporItems'] as $item)
                                <tr>
                                    <td class="px-4 py-3 font-semibold text-ink">{{ $item->subject_name }}</td>
                                    <td class="px-4 py-3 font-mono text-right tabular">{{ $item->score ?? '–' }}</td>
                                    <td class="px-4 py-3 text-center">
                                        @if ($item->score)
                                            <x-ui.badge variant="{{ $item->score >= 85 ? 'success' : ($item->score >= 70 ? 'info' : 'warning') }}">
                                                {{ \App\Support\Rapor::predikat($item->score) }}
                                            </x-ui.badge>
                                        @else
                                            –
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-xs text-ink-soft">{{ $item->description ?? '–' }}</td>
                                </tr>
                            @endforeach
                        </x-ui.table>
                    @endif
                </x-ui.sheet>

                {{-- Kehadiran --}}
                <x-ui.sheet title="Kehadiran" subtitle="Rekap kehadiran bulanan" :pinned="true" :padding="false">
                    <x-ui.table :headers="['Bulan', 'Hadir', 'Sakit', 'Izin', 'Alpha', '% Hadir']">
                        @foreach ($p['kehadiran'] as $k)
                            @php
                                $total = $k['H'] + $k['S'] + $k['I'] + $k['A'];
                                $pct = $total > 0 ? round(($k['H'] / $total) * 100, 1) : 0;
                            @endphp
                            <tr>
                                <td class="px-4 py-3 font-semibold text-ink">{{ $k['label'] }}</td>
                                <td class="px-4 py-3 font-mono text-center tabular text-success">{{ $k['H'] }}</td>
                                <td class="px-4 py-3 font-mono text-center tabular text-warning">{{ $k['S'] }}</td>
                                <td class="px-4 py-3 font-mono text-center tabular text-info">{{ $k['I'] }}</td>
                                <td class="px-4 py-3 font-mono text-center tabular text-danger">{{ $k['A'] }}</td>
                                <td class="px-4 py-3 text-right">
                                    <x-ui.badge variant="{{ $pct >= 90 ? 'success' : ($pct >= 75 ? 'info' : 'warning') }}">{{ $pct }}%</x-ui.badge>
                                </td>
                            </tr>
                        @endforeach
                        <x-slot:footer>
                            <tr>
                                <td class="px-4 py-3 font-bold text-ink">Total</td>
                                <td class="px-4 py-3 text-center font-bold tabular text-success">{{ $p['totalHadir'] }}</td>
                                <td class="px-4 py-3 text-center font-bold tabular text-warning">{{ $p['totalSakit'] }}</td>
                                <td class="px-4 py-3 text-center font-bold tabular text-info">{{ $p['totalIzin'] }}</td>
                                <td class="px-4 py-3 text-center font-bold tabular text-danger">{{ $p['totalAlpha'] }}</td>
                                <td class="px-4 py-3 text-right font-bold">{{ $p['persentaseHadir'] }}%</td>
                            </tr>
                        </x-slot:footer>
                    </x-ui.table>
                </x-ui.sheet>

                {{-- Prestasi --}}
                <x-ui.sheet title="Prestasi" subtitle="Prestasi terverifikasi" :pinned="true" :padding="false">
                    @if ($p['prestasi']->isEmpty())
                        <div class="px-6 py-8 text-center text-sm text-ink-soft">Belum ada prestasi tercatat.</div>
                    @else
                        <x-ui.table :headers="['Tanggal', 'Kegiatan', 'Jenis', 'Tingkat', 'Status']">
                            @foreach ($p['prestasi'] as $item)
                                <tr>
                                    <td class="px-4 py-3 font-mono text-xs tabular">{{ $item->tanggal?->format('d/m/Y') ?? '–' }}</td>
                                    <td class="px-4 py-3 font-semibold text-ink">{{ $item->nama_kegiatan }}</td>
                                    <td class="px-4 py-3">
                                        <x-ui.badge variant="{{ $item->jenis === 'akademik' ? 'info' : 'primary' }}">{{ ucfirst($item->jenis) }}</x-ui.badge>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-ink-soft">{{ ucfirst($item->tingkat) }}</td>
                                    <td class="px-4 py-3 text-right">
                                        <x-ui.badge variant="success">{{ ucfirst($item->status_verifikasi) }}</x-ui.badge>
                                    </td>
                                </tr>
                            @endforeach
                        </x-ui.table>
                    @endif
                </x-ui.sheet>

                {{-- Pelanggaran --}}
                <x-ui.sheet title="Pelanggaran" subtitle="Pelanggaran yang sudah diselesaikan" :pinned="true" :padding="false">
                    @if ($p['pelanggaran']->isEmpty())
                        <div class="px-6 py-8 text-center text-sm text-ink-soft">Tidak ada pelanggaran.</div>
                    @else
                        <x-ui.table :headers="['Tanggal', 'Pelanggaran', 'Tingkat', 'Poin', 'Status']">
                            @foreach ($p['pelanggaran'] as $item)
                                <tr>
                                    <td class="px-4 py-3 font-mono text-xs tabular">{{ $item->tanggal_kejadian?->format('d/m/Y') ?? '–' }}</td>
                                    <td class="px-4 py-3 font-semibold text-ink">{{ $item->kategori }}</td>
                                    <td class="px-4 py-3">
                                        <x-ui.badge variant="{{ match($item->tingkat) { 'berat' => 'danger', 'sedang' => 'warning', default => 'neutral' } }}">{{ ucfirst($item->tingkat) }}</x-ui.badge>
                                    </td>
                                    <td class="px-4 py-3 font-mono text-center tabular">{{ $item->poin }}</td>
                                    <td class="px-4 py-3 text-right">
                                        <x-ui.badge variant="success">{{ ucfirst($item->status_penyelesaian) }}</x-ui.badge>
                                    </td>
                                </tr>
                            @endforeach
                        </x-ui.table>
                    @endif
                </x-ui.sheet>
            </div>

            {{-- Kolom Kanan (1/3) --}}
            <div class="space-y-6">
                {{-- Ringkasan --}}
                <x-ui.sheet title="Ringkasan" :pinned="true">
                    <div class="space-y-3">
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-ink-soft">Kehadiran</span>
                            <span class="font-bold tabular text-ink">{{ $p['persentaseHadir'] }}%</span>
                        </div>
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-ink-soft">Poin Pelanggaran</span>
                            <span class="font-bold tabular {{ $p['totalPoinPelanggaran'] > 50 ? 'text-danger' : 'text-ink' }}">{{ $p['totalPoinPelanggaran'] }}</span>
                        </div>
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-ink-soft">Prestasi</span>
                            <span class="font-bold tabular text-ink">{{ $p['prestasi']->count() }}</span>
                        </div>
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-ink-soft">Ekstrakurikuler</span>
                            <span class="font-bold tabular text-ink">{{ $p['ekskul']->count() }}</span>
                        </div>
                    </div>
                </x-ui.sheet>

                {{-- SPP --}}
                <x-ui.sheet title="SPP" subtitle="Status pembayaran semester ini" :pinned="true" :padding="false">
                    @if ($p['sppTotal'] === 0)
                        <div class="px-6 py-8 text-center text-sm text-ink-soft">Tidak ada data SPP.</div>
                    @else
                        <div class="divide-y divide-rule/70">
                            @foreach ($p['months'] as $bulan)
                                @php $sppItem = $p['spp']->get($bulan); @endphp
                                <div class="flex items-center justify-between px-5 py-3">
                                    <span class="text-sm text-ink">{{ Carbon\Carbon::create(null, $bulan, 1)->locale('id')->translatedFormat('F') }}</span>
                                    @if ($sppItem?->status === 'lunas')
                                        <x-ui.badge variant="success">Lunas</x-ui.badge>
                                    @else
                                        <x-ui.badge variant="neutral">Belum Bayar</x-ui.badge>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                        <div class="border-t border-rule/70 px-5 py-3">
                            <div class="flex items-center justify-between text-sm font-bold text-ink">
                                <span>Terbayar</span>
                                <span>{{ $p['sppLunas'] }}/{{ $p['sppTotal'] }} bulan</span>
                            </div>
                        </div>
                    @endif
                </x-ui.sheet>

                {{-- Ekstrakurikuler --}}
                <x-ui.sheet title="Ekstrakurikuler" :pinned="true">
                    @if ($p['ekskul']->isEmpty())
                        <p class="text-sm text-ink-soft">Belum mengikuti ekstrakurikuler.</p>
                    @else
                        <div class="space-y-3">
                            @foreach ($p['ekskul'] as $eks)
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-ink">{{ $eks['nama'] }}</span>
                                    <x-ui.badge variant="{{ $eks['predikat'] === 'A' ? 'success' : ($eks['predikat'] === 'B' ? 'info' : 'warning') }}">
                                        {{ $eks['predikat'] }} ({{ $eks['rata_poin'] }})
                                    </x-ui.badge>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </x-ui.sheet>

                {{-- QR Code --}}
                <x-ui.sheet title="QR Code" subtitle="Scan untuk verifikasi portofolio">
                    <div class="flex flex-col items-center gap-3">
                        <img src="{{ route('portofolio.qr', $student) }}" alt="QR Code Portofolio" class="size-40 rounded-lg bg-white p-2" />
                        <p class="text-xs text-center text-ink-soft">QR Code ini mengarah ke halaman verifikasi portofolio siswa.</p>
                    </div>
                </x-ui.sheet>
            </div>
        </div>
    </div>

    <div class="mt-6">
        <x-ui.button variant="ghost" icon="arrow-left" href="{{ route('portofolio.index') }}">Kembali ke Pencarian</x-ui.button>
    </div>
</x-layouts.page>
