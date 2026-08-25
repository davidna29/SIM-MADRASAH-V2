# UI Component Library - SIM Madrasah

> Dokumentasi komponen UI Blade yang digunakan di seluruh aplikasi.
> Semua komponen terletak di `resources/views/components/ui/`.

## Overview

SIM Madrasah menggunakan sistem komponen Blade custom yang terinspirasi dari pola shadcn/ui:
- **Variant-based styling** - Setiap komponen mendukung beberapa varian warna
- **Props yang jelas** - Parameter terdefinisi dengan default values
- **Slot untuk konten** - Mendukung konten dinamis via Blade slots
- **Alpine.js integration** - Interaktif tanpa page reload

## Design Tokens

### Warna (dari DESIGN.md)
- **Primary**: `#1b7a45` (Hijau Madrasah) - Tombol aksi utama
- **Paper**: `#f6f1e7` - Latar halaman (hangat)
- **Sheet**: `#ffffff` - Kartu, modal, input
- **Ink**: `#22322a` - Teks utama
- **Rule**: `#e4dcc8` - Garis pembatas

### Typography
- **Display/Body**: Plus Jakarta Sans
- **Data/Mono**: JetBrains Mono (untuk angka, NIS, nominal)

### Spacing & Radius
- **Control radius**: `0.625rem` (tombol, input)
- **Sheet radius**: `0.875rem` (kartu, modal)

---

## Komponen

### 1. Button (`x-ui.button`)

Tombol untuk aksi pengguna.

```blade
{{-- Basic --}}
<x-ui.button>Simpan</x-ui.button>

{{-- Dengan variant --}}
<x-ui.button variant="primary">Simpan</x-ui.button>
<x-ui.button variant="secondary">Batal</x-ui.button>
<x-ui.button variant="danger">Hapus</x-ui.button>
<x-ui.button variant="ghost">Kembali</x-ui.button>

{{-- Dengan icon --}}
<x-ui.button variant="primary" icon="check">Simpan</x-ui.button>
<x-ui.button variant="secondary" icon="pencil" icon-right="arrow-right">Edit</x-ui.button>

{{-- Sebagai link --}}
<x-ui.button variant="primary" href="{{ route('siswa.index') }}">Lihat Siswa</x-ui.button>

{{-- Dengan ukuran --}}
<x-ui.button size="sm">Kecil</x-ui.button>
<x-ui.button size="md">Sedang</x-ui.button>
<x-ui.button size="lg">Besar</x-ui.button>

{{-- Disabled --}}
<x-ui.button disabled>Tidak Aktif</x-ui.button>
```

**Props:**
| Prop | Type | Default | Deskripsi |
|------|------|---------|-----------|
| `variant` | string | `primary` | `primary`, `secondary`, `outline`, `danger`, `success`, `ghost` |
| `size` | string | `md` | `sm`, `md`, `lg` |
| `icon` | string | null | Nama ikon Heroicons (tanpa prefix `svg-`) |
| `iconRight` | string | null | Nama ikon di sebelah kanan |
| `type` | string | `button` | Type HTML button |
| `href` | string | null | Jika diisi, button menjadi link `<a>` |
| `disabled` | bool | false | Disabled state |

---

### 2. Badge (`x-ui.badge`)

Penanda status dengan pin-dot.

```blade
{{-- Basic --}}
<x-ui.badge>Sukses</x-ui.badge>

{{-- Dengan variant --}}
<x-ui.badge variant="success">Lunas</x-ui.badge>
<x-ui.badge variant="warning">Menunggu</x-ui.badge>
<x-ui.badge variant="danger">Ditolak</x-ui.badge>
<x-ui.badge variant="info">Kelas</x-ui.badge>

{{-- Tanpa dot --}}
<x-ui.badge variant="primary" :dot="false">Label</x-ui.badge>

{{-- Dengan icon --}}
<x-ui.badge variant="success" icon="check-circle">Aktif</x-ui.badge>
```

**Props:**
| Prop | Type | Default | Deskripsi |
|------|------|---------|-----------|
| `variant` | string | `neutral` | `neutral`, `success`, `warning`, `danger`, `info`, `primary` |
| `dot` | bool | true | Tampilkan pin-dot |
| `icon` | string | null | Nama ikon Heroicons |

---

### 3. Modal (`x-ui.modal`)

Dialog overlay untuk form atau konfirmasi.

```blade
<x-ui.modal id="modal-tambah" title="Tambah Data">
    {{-- Trigger --}}
    <x-slot:trigger>
        <x-ui.button variant="primary">Tambah</x-ui.button>
    </x-slot:trigger>

    {{-- Konten --}}
    <form>
        <x-ui.field label="Nama">
            <x-ui.input name="nama" />
        </x-ui.field>
    </form>

    {{-- Footer --}}
    <x-slot:footer>
        <x-ui.button variant="ghost" x-on:click="open = false">Batal</x-ui.button>
        <x-ui.button variant="primary">Simpan</x-ui.button>
    </x-slot:footer>
</x-ui.modal>
```

**Props:**
| Prop | Type | Default | Deskripsi |
|------|------|---------|-----------|
| `id` | string | null | ID untuk aria-labelledby |
| `title` | string | null | Judul modal |
| `confirmText` | string | `Simpan` | Teks tombol konfirmasi |
| `variant` | string | `primary` | Variant tombol konfirmasi |

**Slots:**
- `$trigger` - Element pemicu modal
- `$slot` - Konten utama
- `$footer` - Tombol aksi di bawah

**Alpine State:**
- `open` - Boolean, status modal
- `submitting` - Boolean, status submit

---

### 4. Table (`x-ui.table`)

Tabel data dengan header, body, dan footer.

```blade
<x-ui.table :headers="['Nama', 'NIS', 'Kelas', 'Status']">
    @foreach($siswa as $s)
        <tr>
            <td class="px-4 py-3">{{ $s->name }}</td>
            <td class="px-4 py-3 font-mono">{{ $s->nis }}</td>
            <td class="px-4 py-3">{{ $s->classGroup->name }}</td>
            <td class="px-4 py-3 text-right">
                <x-ui.badge variant="success">Aktif</x-ui.badge>
            </td>
        </tr>
    @endforeach
</x-ui.table>

{{-- Dengan empty state --}}
<x-ui.table :headers="['Nama', '']" :empty="true">
    <x-slot:emptySlot>
        <div class="py-8 text-center text-ink-soft">
            Tidak ada data ditemukan.
        </div>
    </x-slot:emptySlot>
</x-ui.table>

{{-- Dengan footer --}}
<x-ui.table :headers="['Item', 'Jumlah']" :footer="true">
    <x-slot:footer>
        <tr>
            <td class="px-4 py-3 font-bold">Total</td>
            <td class="px-4 py-3 text-right font-bold">100</td>
        </tr>
    </x-slot:footer>
</x-ui.table>
```

**Props:**
| Prop | Type | Default | Deskripsi |
|------|------|---------|-----------|
| `headers` | array | [] | Array nama kolom header |
| `footer` | bool | null | Tampilkan footer slot |
| `empty` | bool | false | Tampilkan empty state |
| `emptySlot` | string | null | Custom empty state content |

---

### 5. Select (`x-ui.select`)

Dropdown select dengan options.

```blade
{{-- Basic --}}
<x-ui.select name="kelas" :options="$classes->pluck('name', 'id')" />

{{-- Dengan placeholder --}}
<x-ui.select name="status" :options="['aktif' => 'Aktif', 'nonaktif' => 'Nonaktif']" placeholder="Pilih status" />

{{-- Dengan selected value --}}
<x-ui.select name="gender" :options="['L' => 'Laki-laki', 'P' => 'Perempuan']" :selected="old('gender')" />

{{-- Inline (tidak full width) --}}
<x-ui.select name="type" :full="false" class="w-36" :options="['siswa' => 'Siswa', 'pegawai' => 'Pegawai']" />
```

**Props:**
| Prop | Type | Default | Deskripsi |
|------|------|---------|-----------|
| `error` | string | null | Pesan error |
| `options` | array | [] | Array key=>value options |
| `full` | bool | true | Full width |
| `selected` | mixed | null | Value yang dipilih |

---

### 6. Input (`x-ui.input`)

Input field untuk form.

```blade
{{-- Basic --}}
<x-ui.input name="nama" placeholder="Masukkan nama" />

{{-- Dengan type --}}
<x-ui.input type="email" name="email" placeholder="email@contoh.com" />
<x-ui.input type="password" name="password" />
<x-ui.input type="date" name="tanggal" />
<x-ui.input type="number" name="jumlah" min="0" />

{{-- Dengan prefix --}}
<x-ui.input name="phone" prefix="+62" placeholder="812345678" />

{{-- Dengan error --}}
<x-ui.input name="nis" :error="$errors->first('nis')" />

{{-- Dengan value --}}
<x-ui.input name="nis" :value="old('nis', $editing ? $student->nis : '')" />
```

**Props:**
| Prop | Type | Default | Deskripsi |
|------|------|---------|-----------|
| `type` | string | `text` | HTML input type |
| `error` | string | null | Pesan error |
| `prefix` | string | null | Teks prefix (misal: +62) |

---

### 7. Sheet (`x-ui.sheet`)

Kartu container untuk konten.

```blade
{{-- Basic --}}
<x-ui.sheet>
    <p>Isi kartu</p>
</x-ui.sheet>

{{-- Dengan header --}}
<x-ui.sheet title="Detail Siswa" subtitle="Biodata lengkap">
    <p>Konten di sini</p>
</x-ui.sheet>

{{-- Dengan actions --}}
<x-ui.sheet title="Daftar Guru" :actions="'<x-ui.button variant=&quot;primary&quot; icon=&quot;plus&quot;>Tambah</x-ui.button>'">
    <p>Tabel guru</p>
</x-ui.sheet>

{{-- Dengan pinned (pin-dot) --}}
<x-ui.sheet title="Kehadiran" :pinned="true">
    <p>Data kehadiran</p>
</x-ui.sheet>

{{-- Dengan ruled (kertas bergaris) --}}
<x-ui.sheet title="Formulir" :ruled="true">
    <p>Form input</p>
</x-ui.sheet>

{{-- Tanpa padding --}}
<x-ui.sheet title="Tabel" :padding="false">
    <x-ui.table :headers="['Kolom 1', 'Kolom 2']">
        {{-- rows --}}
    </x-ui.table>
</x-ui.sheet>
```

**Props:**
| Prop | Type | Default | Deskripsi |
|------|------|---------|-----------|
| `title` | string | null | Judul sheet |
| `subtitle` | string | null | Subtitle di bawah judul |
| `actions` | string | null | HTML tombol aksi di header |
| `pinned` | bool | false | Tampilkan pin-dot |
| `padding` | bool | true | Padding internal |
| `ruled` | bool | false | Latar kertas bergaris |
| `class` | string | null | Additional CSS classes |

---

### 8. Pagination (`x-ui.pagination`)

Navigasi halaman.

```blade
<x-ui.pagination 
    :current="$products->currentPage()" 
    :last="$products->lastPage()" 
/>
```

**Props:**
| Prop | Type | Default | Deskripsi |
|------|------|---------|-----------|
| `current` | int | 1 | Halaman saat ini |
| `last` | int | 1 | Halaman terakhir |
| `baseUrl` | string | null | URL basis (default: current URL) |

---

### 9. Alert (`x-ui.alert`)

Notifikasi atau pesan.

```blade
{{-- Basic --}}
<x-ui.alert variant="success">Data berhasil disimpan!</x-ui.alert>

{{-- Dismissible --}}
<x-ui.alert variant="danger" dismissible>
    <strong>Error!</strong> Terjadi kesalahan saat memproses data.
</x-ui.alert>

{{-- Dengan session --}}
@if(session('status'))
    <x-ui.alert variant="success" dismissible>{{ session('status') }}</x-ui.alert>
@endif

@if($errors->any())
    <x-ui.alert variant="danger" dismissible>
        <ul class="list-disc list-inside">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </x-ui.alert>
@endif
```

**Props:**
| Prop | Type | Default | Deskripsi |
|------|------|---------|-----------|
| `variant` | string | `info` | `info`, `success`, `warning`, `danger` |
| `dismissible` | bool | false | Bisa ditutup user |

---

### 10. Field (`x-ui.field`)

Wrapper untuk form field dengan label dan error.

```blade
{{-- Basic --}}
<x-ui.field label="Nama Lengkap">
    <x-ui.input name="name" />
</x-ui.field>

{{-- Required --}}
<x-ui.field label="NIS" required>
    <x-ui.input name="nis" />
</x-ui.field>

{{-- Dengan hint --}}
<x-ui.field label="NIK" hint="Nomor Induk Kependudukan - 16 digit">
    <x-ui.input name="nik" maxlength="16" />
</x-ui.field>

{{-- Dengan error --}}
<x-ui.field label="Email" :error="$errors->first('email')">
    <x-ui.input type="email" name="email" />
</x-ui.field>
```

**Props:**
| Prop | Type | Default | Deskripsi |
|------|------|---------|-----------|
| `label` | string | null | Label field |
| `required` | bool | false | Tanda wajib (*) |
| `hint` | string | null | Teks bantuan |
| `error` | string | null | Pesan error |

---

### 11. Form Section (`x-ui.form-section`)

Section/grouping dalam form.

```blade
<x-ui.form-section title="Data Pribadi" description="Informasi dasar siswa.">
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
        <x-ui.field label="Nama">
            <x-ui.input name="name" />
        </x-ui.field>
        <x-ui.field label="NIS">
            <x-ui.input name="nis" />
        </x-ui.field>
    </div>
</x-ui.form-section>
```

**Props:**
| Prop | Type | Default | Deskripsi |
|------|------|---------|-----------|
| `title` | string | null | Judul section |
| `description` | string | null | Deskripsi section |
| `aside` | string | null | Konten samping |

---

## Best Practices

### 1. Konsistensi Variant
Gunakan variant yang konsisten di seluruh aplikasi:
- **Primary** (hijau) untuk aksi utama (Simpan, Tambah, Kirim)
- **Secondary** untuk aksi sekunder (Batal, Kembali)
- **Danger** untuk aksi destruktif (Hapus, Batalkan)
- **Ghost** untuk navigasi atau aksi minor

### 2. Icon + Text
Selalu sertakan teks pada ikon:
```blade
{{-- ✅ Benar --}}
<x-ui.button icon="trash">Hapus</x-ui.button>

{{-- ❌ Salah --}}
<x-ui.button icon="trash"></x-ui.button>
```

### 3. Status Badge
Tampilkan status dengan badge + label:
```blade
{{-- ✅ Benar --}}
<x-ui.badge variant="success">Lunas</x-ui.badge>

{{-- ❌ Salah --}}
<span class="text-green-500">✓</span>
```

### 4. Error Handling
Gunakan `x-ui.field` untuk validasi:
```blade
<x-ui.field label="Email" :error="$errors->first('email')">
    <x-ui.input type="email" name="email" :error="$errors->first('email')" />
</x-ui.field>
```

### 5. Empty State
Sertakan empty state untuk tabel:
```blade
<x-ui.table :headers="['Nama', '']" :empty="$items->isEmpty()">
    <x-slot:emptySlot>
        <x-empty-state 
            icon="document-text" 
            title="Belum ada data" 
            description="Mulai menambahkan data baru." 
        />
    </x-slot:emptySlot>
</x-ui.table>
```

### 6. Responsive
Gunakan responsive classes:
```blade
<x-ui.table :headers="['Nama', 'Kelas', 'Status']">
    <tr>
        <td class="px-4 py-3">{{ $siswa->name }}</td>
        <td class="hidden px-4 py-3 sm:table-cell">{{ $siswa->classGroup->name }}</td>
        <td class="px-4 py-3 text-right">
            <x-ui.badge variant="success">Aktif</x-ui.badge>
        </td>
    </tr>
</x-ui.table>
```

---

## Troubleshooting

### Modal tidak terbuka
Pastikan Alpine.js terload dan `x-cloak` di CSS:
```css
[x-cloak] { display: none !important; }
```

### Style tidak terapply
Jalankan:
```bash
npm run build
# atau
npm run dev
```

### Icon tidak muncul
Pastikan icon name sesuai Heroicons:
```blade
{{-- ✅ Benar --}}
<x-ui.button icon="check">Simpan</x-ui.button>

{{-- ❌ Salah --}}
<x-ui.button icon="check-circle">Simpan</x-ui.button>
```

---

## Referensi

- [DESIGN.md](../DESIGN.md) - Design system lengkap
- [PRD Sim Madrasah](./PRD%20sim%20madrasah.md) - Product Requirements
- [AI-HANDOFF.md](./AI-HANDOFF.md) - Status proyek dan konvensi
