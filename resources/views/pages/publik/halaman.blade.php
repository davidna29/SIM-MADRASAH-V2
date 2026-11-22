<x-layouts.publik :title="$pageTitle" :description="$pageDescription">
    <div class="mx-auto max-w-4xl">
        <x-ui.breadcrumb :items="[['label' => 'Beranda', 'href' => route('publik.beranda')], ['label' => $pageTitle]]" />

        <header class="mt-8 border-b border-rule-strong/70 pb-7 sm:pb-9">
            <h1 class="text-3xl font-extrabold leading-tight tracking-tight text-ink sm:text-4xl">{{ $pageTitle }}</h1>
            @if ($page?->subtitle)
                <p class="mt-3 max-w-2xl text-base leading-relaxed text-ink-soft">{{ $page->subtitle }}</p>
            @elseif ($page?->summary)
                <p class="mt-3 max-w-2xl text-base leading-relaxed text-ink-soft">{{ $page->summary }}</p>
            @endif
        </header>

        @if ($page?->featured_image)
            <figure class="mt-8 overflow-hidden rounded-sheet bg-paper-deep shadow-sheet ring-1 ring-inset ring-rule/60">
                <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($page->featured_image) }}" alt="{{ $page->image_alt ?: $pageTitle }}" class="max-h-[34rem] w-full object-cover" fetchpriority="high">
                @if ($page->image_alt)
                    <figcaption class="px-4 py-3 text-xs text-ink-faint">{{ $page->image_alt }}</figcaption>
                @endif
            </figure>
        @endif

        @if ($page?->body)
            <div class="prose-madrasah mt-8 max-w-[70ch] whitespace-pre-line text-[15px] leading-8 text-ink">{{ $page->body }}</div>
        @elseif ($slug === 'kebijakan-privasi')
            <div class="mt-8 space-y-5 rounded-sheet bg-sheet p-6 shadow-sheet ring-1 ring-inset ring-rule/60 sm:p-8">
                <p class="text-sm leading-relaxed text-ink">Data yang dimasukkan pada formulir PPDB dan siswa pindahan digunakan untuk menerima pendaftaran, memeriksa kelengkapan, dan menghubungi orang tua/wali terkait proses penerimaan.</p>
                <p class="text-sm leading-relaxed text-ink">Formulir dapat memuat data identitas, alamat, keluarga, kesehatan, serta tautan dokumen. Data tersebut bukan informasi publik dan tidak ditampilkan pada halaman website.</p>
                <p class="text-sm leading-relaxed text-ink">Saat membagikan berkas, atur akses hanya untuk panitia atau akun resmi madrasah yang menangani pendaftaran; jangan gunakan setelan akses publik. Periode penyimpanan berkas perlu ditetapkan dan ditinjau oleh pihak madrasah.</p>
                <p class="text-sm leading-relaxed text-ink">Untuk pertanyaan atau permintaan koreksi data pendaftaran, silakan hubungi madrasah melalui informasi pada halaman Kontak.</p>
                <p class="border-t border-rule/70 pt-4 text-xs leading-relaxed text-ink-faint">Pemberitahuan ini perlu ditinjau oleh pengelola madrasah sebelum pendaftaran dibuka.</p>
            </div>
        @else
            <div class="mt-8 rounded-sheet bg-sheet p-7 shadow-sheet ring-1 ring-inset ring-rule/60 sm:p-10">
                <p class="text-lg font-bold text-ink">Informasi sedang disiapkan.</p>
                <p class="mt-2 max-w-prose text-sm leading-relaxed text-ink-soft">Tim madrasah sedang melengkapi halaman ini. Sementara itu, Anda dapat membaca berita terbaru atau menghubungi madrasah.</p>
                <div class="mt-5 flex flex-wrap gap-3">
                    <x-ui.button variant="primary" :href="route('publik.berita.index')">Lihat berita</x-ui.button>
                    <x-ui.button variant="secondary" :href="route('publik.kontak')">Hubungi madrasah</x-ui.button>
                </div>
            </div>
        @endif
    </div>
</x-layouts.publik>
