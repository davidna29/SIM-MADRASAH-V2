<x-layouts.publik :title="$page?->title ?: 'Kontak & Lokasi'" :description="$page?->meta_description ?: $page?->summary">
    @php
        $address = collect([
            $siteSettings->get('madrasah_jalan'),
            $siteSettings->get('madrasah_desa'),
            $siteSettings->get('madrasah_kecamatan'),
            $siteSettings->get('madrasah_kabupaten'),
            $siteSettings->get('madrasah_provinsi'),
            $siteSettings->get('madrasah_kode_pos'),
        ])->filter()->implode(', ');
        $latitude = $siteSettings->get('madrasah_latitude');
        $longitude = $siteSettings->get('madrasah_longitude');
        $mapUrl = is_numeric($latitude) && is_numeric($longitude)
            ? 'https://www.openstreetmap.org/?mlat='.$latitude.'&mlon='.$longitude.'#map=16/'.$latitude.'/'.$longitude
            : null;
    @endphp

    <div class="mx-auto max-w-6xl">
        <x-ui.breadcrumb :items="[['label' => 'Beranda', 'href' => route('publik.beranda')], ['label' => 'Kontak & Lokasi']]" />
        <header class="mt-8 max-w-3xl">
            <h1 class="text-3xl font-extrabold tracking-tight text-ink sm:text-4xl">Kontak & Lokasi</h1>
            @if ($page?->subtitle || $page?->summary)
                <p class="mt-3 text-base leading-relaxed text-ink-soft">{{ $page->subtitle ?: $page->summary }}</p>
            @else
                <p class="mt-3 text-base leading-relaxed text-ink-soft">Informasi untuk menghubungi dan menemukan lokasi madrasah.</p>
            @endif
        </header>

        <div class="mt-9 grid grid-cols-1 gap-8 lg:grid-cols-[1fr_0.85fr] lg:gap-12">
            <section aria-labelledby="alamat-heading">
                <h2 id="alamat-heading" class="text-xl font-extrabold tracking-tight text-ink">Alamat Madrasah</h2>
                @if ($address !== '')
                    <address class="mt-4 not-italic text-base leading-relaxed text-ink-soft">{{ $address }}</address>
                @else
                    <p class="mt-4 text-sm text-ink-faint">Alamat belum diumumkan.</p>
                @endif

                @if ($mapUrl)
                    <a href="{{ $mapUrl }}" target="_blank" rel="noopener noreferrer" class="mt-5 inline-flex min-h-11 items-center gap-2 rounded-control bg-primary-soft px-4 text-sm font-bold text-primary-strong transition hover:bg-primary/15 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary">
                        <x-svg-map-pin class="size-4" aria-hidden="true" /> Buka lokasi di OpenStreetMap
                    </a>
                @endif

                @if ($page?->body)
                    <div class="mt-8 whitespace-pre-line border-t border-rule/70 pt-6 text-sm leading-relaxed text-ink">{{ $page->body }}</div>
                @endif
            </section>

            <section aria-labelledby="saluran-heading" class="border-t border-rule-strong/70 pt-7 lg:border-l lg:border-t-0 lg:pl-10 lg:pt-0">
                <h2 id="saluran-heading" class="text-xl font-extrabold tracking-tight text-ink">Saluran Informasi</h2>
                <div class="mt-4 divide-y divide-rule/70 border-y border-rule/70">
                    @if ($siteSettings->get('madrasah_phone'))
                        <a href="tel:{{ preg_replace('/\D+/', '', $siteSettings->get('madrasah_phone')) }}" class="flex min-h-16 items-center justify-between gap-4 py-3 text-sm hover:text-primary-strong">
                            <span><span class="block text-xs font-semibold text-ink-faint">Telepon</span><span class="mt-1 block font-bold text-ink">{{ $siteSettings->get('madrasah_phone') }}</span></span>
                            <x-svg-phone class="size-5 text-primary" aria-hidden="true" />
                        </a>
                    @endif
                    @if ($siteSettings->get('madrasah_email'))
                        <a href="mailto:{{ $siteSettings->get('madrasah_email') }}" class="flex min-h-16 items-center justify-between gap-4 py-3 text-sm hover:text-primary-strong">
                            <span class="min-w-0"><span class="block text-xs font-semibold text-ink-faint">Email</span><span class="mt-1 block break-all font-bold text-ink">{{ $siteSettings->get('madrasah_email') }}</span></span>
                            <x-svg-envelope class="size-5 shrink-0 text-primary" aria-hidden="true" />
                        </a>
                    @endif
                    @if ($siteSettings->get('madrasah_website'))
                        <a href="{{ $siteSettings->get('madrasah_website') }}" target="_blank" rel="noopener noreferrer" class="flex min-h-16 items-center justify-between gap-4 py-3 text-sm hover:text-primary-strong">
                            <span><span class="block text-xs font-semibold text-ink-faint">Website</span><span class="mt-1 block font-bold text-ink">{{ $siteSettings->get('madrasah_website') }}</span></span>
                            <x-svg-globe-alt class="size-5 text-primary" aria-hidden="true" />
                        </a>
                    @endif
                    @if ($siteSettings->get('madrasah_jam_layanan'))
                        <div class="flex min-h-16 items-center justify-between gap-4 py-3 text-sm">
                            <span><span class="block text-xs font-semibold text-ink-faint">Jam layanan</span><span class="mt-1 block font-bold text-ink">{{ $siteSettings->get('madrasah_jam_layanan') }}</span></span>
                            <x-svg-clock class="size-5 text-primary" aria-hidden="true" />
                        </div>
                    @endif
                </div>

                @php
                    $socialLinks = [
                        'Facebook' => $siteSettings->get('madrasah_facebook'),
                        'Instagram' => $siteSettings->get('madrasah_instagram'),
                        'YouTube' => $siteSettings->get('madrasah_youtube'),
                        'TikTok' => $siteSettings->get('madrasah_tiktok'),
                    ];
                    $socialLinks = array_filter($socialLinks);
                @endphp
                @if ($socialLinks)
                    <div class="mt-6">
                        <h3 class="text-sm font-bold text-ink">Media sosial resmi</h3>
                        <div class="mt-3 flex flex-wrap gap-2">
                            @foreach ($socialLinks as $label => $url)
                                <a href="{{ $url }}" target="_blank" rel="noopener noreferrer" class="inline-flex min-h-10 items-center rounded-full bg-paper-deep px-4 text-sm font-semibold text-ink hover:bg-primary-soft hover:text-primary-strong">{{ $label }}</a>
                            @endforeach
                        </div>
                    </div>
                @endif
            </section>
        </div>
    </div>
</x-layouts.publik>
