<x-layouts.page
    :title="'Halaman Publik'"
    :roleLabel="$roleLabel"
    :breadcrumb="$breadcrumb"
    active-route="cms.halaman.index">
    <div class="mx-auto max-w-6xl">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <h1 class="text-2xl font-extrabold tracking-tight text-ink sm:text-3xl">Halaman Publik</h1>
                <p class="mt-1.5 max-w-prose text-sm leading-relaxed text-ink-soft">Kelola profil, sambutan, visi-misi, dan informasi halaman website.</p>
            </div>
            <x-ui.button variant="primary" icon="plus" :href="route('cms.halaman.create')">Tambah Halaman</x-ui.button>
        </div>

        @if (session('status'))
            <div class="mt-6"><x-ui.alert variant="success" dismissible>{{ session('status') }}</x-ui.alert></div>
        @endif

        <x-ui.sheet class="mt-6" padding="false">
            <form method="GET" action="{{ route('cms.halaman.index') }}" class="flex flex-col gap-3 border-b border-rule/70 px-5 py-4 sm:flex-row sm:items-end sm:px-6">
                <x-ui.field label="Cari judul">
                    <x-ui.input name="q" :value="request('q')" placeholder="Contoh: Sejarah Madrasah" />
                </x-ui.field>
                <x-ui.field label="Status">
                    <x-ui.select name="status" :options="['draft' => 'Draf', 'publik' => 'Publik']" :selected="request('status')" placeholder="Semua status" />
                </x-ui.field>
                <x-ui.button type="submit" variant="secondary" icon="magnifying-glass">Cari</x-ui.button>
            </form>

            <div class="overflow-x-auto">
                <table class="w-full min-w-[700px] text-left text-sm">
                    <thead class="border-b border-rule-strong text-xs font-bold uppercase tracking-wide text-ink-soft">
                        <tr><th class="px-5 py-3 sm:px-6">Halaman</th><th class="px-4 py-3">Slug</th><th class="px-4 py-3">Status</th><th class="px-5 py-3 text-right sm:px-6">Tindakan</th></tr>
                    </thead>
                    <tbody class="divide-y divide-rule/70">
                        @forelse ($pages as $page)
                            <tr class="hover:bg-paper/60">
                                <td class="px-5 py-4 sm:px-6">
                                    <p class="font-bold text-ink">{{ $page->title }}</p>
                                    @if ($page->summary)<p class="mt-1 max-w-md text-xs text-ink-faint">{{ \Illuminate\Support\Str::limit($page->summary, 100) }}</p>@endif
                                </td>
                                <td class="px-4 py-4 font-mono text-xs text-ink-faint">{{ $page->slug }}</td>
                                <td class="px-4 py-4"><x-ui.badge :variant="$page->status === 'publik' ? 'success' : 'neutral'">{{ $page->status === 'publik' ? 'Publik' : 'Draf' }}</x-ui.badge></td>
                                <td class="px-5 py-4 text-right sm:px-6">
                                    <div class="inline-flex flex-wrap justify-end gap-2">
                                        @if ($page->status === 'publik')
                                            <x-ui.button variant="ghost" size="sm" icon="arrow-top-right-on-square" :href="route('publik.halaman', $page)">Lihat</x-ui.button>
                                        @endif
                                        <x-ui.button variant="secondary" size="sm" icon="pencil-square" :href="route('cms.halaman.edit', $page)">Ubah</x-ui.button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="px-5 py-10 text-center sm:px-6"><p class="font-semibold text-ink">Belum ada halaman.</p><p class="mt-1 text-xs text-ink-faint">Buat halaman baru, lalu tinjau sebelum menerbitkannya.</p></td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-5 py-4 sm:px-6"><x-ui.pagination :current="$pages->currentPage()" :last="$pages->lastPage()" /></div>
        </x-ui.sheet>
    </div>
</x-layouts.page>
