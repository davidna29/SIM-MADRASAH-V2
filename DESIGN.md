---
name: SIM Madrasah
description: Sistem informasi manajemen madrasah berbahasa Indonesia — dunia papan pengumuman (mading) di atas kertas hangat.
colors:
  paper: "#f6f1e7"
  paper-deep: "#ece5d3"
  sheet: "#ffffff"
  ink: "#22322a"
  ink-soft: "#55665b"
  ink-faint: "#5f7067"
  rule: "#e4dcc8"
  rule-strong: "#d3c8ad"
  board: "#12402f"
  board-deep: "#0c3023"
  board-soft: "#1e5a42"
  board-ink: "#e9f4ee"
  primary: "#1b7a45"
  primary-strong: "#11602f"
  primary-soft: "#e3f1e8"
  success: "#157a3c"
  success-soft: "#e4f3e9"
  warning: "#9c4308"
  warning-soft: "#fdf1dc"
  danger: "#b42318"
  danger-soft: "#fdeae7"
  info: "#1d5fa5"
  info-soft: "#e6f0fa"
typography:
  scale:
    print: "9px"
    xs: "10px"
    sm: "11px"
    md: "13px"
    lg: "15px"
  display:
    fontFamily: "Plus Jakarta Sans, ui-sans-serif, system-ui, sans-serif"
    fontWeight: 800
    lineHeight: 1.1
    letterSpacing: "-0.02em"
  headline:
    fontFamily: "Plus Jakarta Sans, ui-sans-serif, system-ui, sans-serif"
    fontWeight: 700
    fontSize: "1.5rem"
    lineHeight: 1.2
    letterSpacing: "-0.02em"
  title:
    fontFamily: "Plus Jakarta Sans, ui-sans-serif, system-ui, sans-serif"
    fontWeight: 700
    fontSize: "1.125rem"
    lineHeight: 1.3
  body:
    fontFamily: "Plus Jakarta Sans, ui-sans-serif, system-ui, sans-serif"
    fontWeight: 400
    fontSize: "0.875rem"
    lineHeight: 1.6
  label:
    fontFamily: "Plus Jakarta Sans, ui-sans-serif, system-ui, sans-serif"
    fontWeight: 600
    fontSize: "0.75rem"
    lineHeight: 1.4
  caption:
    fontFamily: "Plus Jakarta Sans, ui-sans-serif, system-ui, sans-serif"
    fontWeight: 700
    fontSize: "0.625rem"
    lineHeight: 1.4
  item:
    fontFamily: "Plus Jakarta Sans, ui-sans-serif, system-ui, sans-serif"
    fontWeight: 600
    fontSize: "0.8125rem"
    lineHeight: 1.4
  meta:
    fontFamily: "JetBrains Mono, ui-monospace, Menlo, monospace"
    fontWeight: 600
    fontSize: "0.6875rem"
    fontFeature: "\"tnum\""
  data:
    fontFamily: "JetBrains Mono, ui-monospace, Menlo, monospace"
    fontWeight: 600
    fontFeature: "\"tnum\""
  print:
    fontFamily: "DejaVu Sans, DejaVu Sans Mono, sans-serif"
    fontWeight: 400
    fontSize: "15px"
    lineHeight: 1.4
rounded:
  sm: "0.625rem"
  md: "0.875rem"
spacing:
  xs: "4px"
  sm: "8px"
  md: "16px"
  lg: "24px"
  xl: "32px"
components:
  button-primary:
    backgroundColor: "{colors.primary}"
    textColor: "#ffffff"
    rounded: "{rounded.sm}"
    padding: "0 16px"
    height: "40px"
  button-secondary:
    backgroundColor: "{colors.sheet}"
    textColor: "{colors.ink}"
    rounded: "{rounded.sm}"
    padding: "0 16px"
    height: "40px"
  badge-success:
    backgroundColor: "{colors.success-soft}"
    textColor: "{colors.success}"
    rounded: "999px"
  badge-warning:
    backgroundColor: "{colors.warning-soft}"
    textColor: "{colors.warning}"
    rounded: "999px"
  badge-danger:
    backgroundColor: "{colors.danger-soft}"
    textColor: "{colors.danger}"
    rounded: "999px"
  badge-info:
    backgroundColor: "{colors.info-soft}"
    textColor: "{colors.info}"
    rounded: "999px"
  sheet:
    backgroundColor: "{colors.sheet}"
    rounded: "{rounded.md}"
  kpi:
    backgroundColor: "{colors.sheet}"
    rounded: "{rounded.md}"
  input:
    backgroundColor: "{colors.sheet}"
    textColor: "{colors.ink}"
    rounded: "{rounded.sm}"
    height: "40px"
---

# Design System: SIM Madrasah

## Overview

**Creative North Star: "Papan Pengumuman Madrasah (Mading)"**

Seluruh sistem operasional madrasah disajikan sebagai dinding papan pengumuman yang tertata: papan hijau madrasah di kiri sebagai indeks, lembaran kertas putih bergaris sebagai kartu, dan penanda status sebagai pin berwarna yang disematkan. Setiap modul adalah sebuah papan; setiap catatan adalah lembar; persetujuan berarti "menyematkan" sebuah lembar; riwayat tidak pernah hilang dari papan. Sistem ini menolak dashboard admin abu-abu generik yang biasa dikirim kategori ini.

Personality: terang, sederhana, dan hangat — dibangun untuk guru dan pegawai madrasah yang belum terbiasa aplikasi kompleks. Kertas hangat sebagai tanah memberi kehangatan; hijau madrasah sebagai papan memberi otoritas institusional; lembaran bergaris memberi keteraturan yang akrab dengan administrasi kertas yang selama ini dipakai. Jenis huruf Plus Jakarta Sans — dirancang di Jakarta — memberi kesejajaran budaya, sementara JetBrains Mono menjaga angka (NIS, nominal Rupiah, tanggal) tetap presisi dan tabular.

**Key Characteristics:**
- Kertas hangat sebagai tanah, lembar putih sebagai kartu, papan hijau sebagai navigasi.
- Status selalu sebagai pin berwarna + label teks (tidak pernah hanya ikon).
- Angka data memakai font monospace tabular (digit-bank KPI).
- Tindakan utama hijau; teks selalu menemani ikon.
- Bahasa antarmuka Bahasa Indonesia.

## Colors

Palet terang dengan satu aksen hijau yang memegang struktur navigasi dan tindakan utama; status memakai empat peran warna yang didefinisikan PRD.

### Primary
- **Hijau Madrasah** (#1b7a45): tombol primer, tautan aktif, pin-penanda utama, fokus, dan pilihan teks. Dipakai konsisten untuk satu tindakan per layar.
- **Hijau Madrasah Pekat** (#11602f): hover/aktif tombol primer, teks label hijau.
- **Hijau Lembut** (#e3f1e8): latar tautan/aksi sekunder hijau.

### Secondary
- **Hijau Papan** (#12402f): latar sidebar (papan) dan permukaan terangkat gelap; aksen pada toast.
- **Hijau Papan Pekat** (#0c3023): overlay gelap (modal backdrop).
- **Hijau Papan Lembut** (#1e5a42): item navigasi aktif di atas papan.
- **Tinta Papan** (#e9f4ee): teks di atas permukaan papan.

### Tertiary
- **Hijau Primer** (digunakan ulang sebagai peran utama) dan **hijau sukses** dipisahkan sebagai dua peran berbeda: primer untuk tindakan, sukses untuk status.

### Neutral
- **Kertas** (#f6f1e7): latar halaman; hangat, bukan putih bersih.
- **Kertas Pekat** (#ece5d3): hover latar, permukaan filter.
- **Lembar** (#ffffff): kartu, sheet, input, modal.
- **Tinta** (#22322a): teks utama.
- **Tinta Lembut** (#55665b): teks sekunder.
- **Tinta Samar** (#5f7067): label kecil, meta, timestamp (≥ 4.5:1 di atas kertas).
- **Garis** (#e4dcc8): pembatas halus antar baris.
- **Garis Pekat** (#d3c8ad): pembatas header tabel, ikon ring.

### Status
- **Sukses / Lunas** (#157a3c di atas #e4f3e9): status lunas, selesai, disetujui, online.
- **Peringatan / Cicilan** (#9c4308 di atas #fdf1dc): cicilan, menunggu, peringatan (≥ 4.5:1).
- **Bahaya / Error** (#b42318 di atas #fdeae7): error, telat, ditolak.
- **Informasi** (#1d5fa5 di atas #e6f0fa): info, kelas, agenda.

### Named Rules
**The Pin-As-Status Rule.** Status ditampilkan sebagai pin berwarna dengan label teks berdampingan — warna tidak pernah menjadi satu-satunya pembawa makna.

**The One-Action Rule.** Satu hijau primer per kartu/layar. Peran primer adalah tindakan paling penting; tindakan kedua memakai sekunder/ghost.

## Typography

**Display / Body Font:** Plus Jakarta Sans (dengan ui-sans-serif, system-ui, sans-serif sebagai fallback)
**Label / Mono Font:** JetBrains Mono (dengan ui-monospace, Menlo, monospace sebagai fallback)

**Character:** Plus Jakarta Sans adalah grotesk humanis yang hangat namun tegas — dirancang di Jakarta, cocok untuk bahasa Indonesia dan lembaga pendidikan. JetBrains Mono memberi kontras "data meja" pada angka tabular: NIS, nominal, tanggal, pagination, ID lembar.

### Hierarchy
- **Display** (800, 1.5–1.875rem, lh 1.1, tracking −0.02em): judul halaman ("Papan Pengawasan", "Data Siswa").
- **Headline** (700, 1.5rem, lh 1.2, tracking −0.02em): judul section sheet.
- **Title** (700, 1.125rem, lh 1.3): judul kartu/header.
- **Body** (400, 0.875rem, lh 1.6, lebar maks ~65–75ch untuk paragraf): isi, deskripsi.
- **Label** (600, 0.75rem, lh 1.4): label form, meta, timestamp, badge.
- **Caption** (700, 0.625rem, lh 1.4): label grup navigasi sidebar.
- **Item** (600, 0.8125rem, lh 1.4): baris daftar "Perlu Tindakan", detail.
- **Meta** (JetBrains Mono 600, 0.6875rem, `tabular-nums`): ID lembar (P-0042), NIS kecil.
- **Data** (JetBrains Mono 600, `tabular-nums`): KPI digit-bank, NIS, nominal Rupiah, pagination, tanggal.
- **Print (PDF rapor)** (DejaVu Sans, 11px dasar): hanya untuk template cetak DomPDF (`resources/views/pdf/`), yang tidak bisa memakai font web; kop 15px, judul section 13px, label tabel 10px.

### Named Rules
**The Digit-Bank Rule.** Semua angka data memakai font mono tabular (JetBrains Mono + `font-variant-numeric: tabular-nums`), sehingga kolom angka berbaris tegak dan berubah nilai tanpa bergeser.

**The Teks-Temani-Ikon Rule.** Ikon selalu disertai label teks. Tidak ada navigasi atau aksi ikon-tanpa-teks (PRD: hindari ikon tanpa teks untuk pengguna baru).

## Layout

Grid 12 kolom responsif; konten dalam `max-w-7xl`. Sidebar papan selebar 288px (sticky di desktop, off-canvas drawer di mobile), topbar sticky di atas konten kertas, footer di bawah.

- **Desktop (≥ 1024px):** sidebar papan kiri 288px sticky + konten `px-6 lg:px-8 py-8`.
- **Mobile (< 1024px):** sidebar menjadi drawer off-canvas yang dibuka lewat hamburger di topbar, dengan backdrop gelap; konten `px-4 py-6`.
- **Irama ruang:** `space-y-6` antar kartu, `gap-4` antar KPI, header kartu `px-5 py-4 sm:px-6`, isi kartu `px-5 py-5 sm:px-6`. Lebih banyak ruang di atas judul daripada di bawahnya.
- **Tabel:** bungkus `overflow-x-auto` untuk scroll horizontal internal di layar sempit; pagination memakai `flex-wrap` dengan label "Sebelumnya/Berikutnya" yang menyembunyikan teksnya di mobile (`hidden sm:inline`).
- **Kartu KPI:** `grid-cols-1 sm:grid-cols-2 xl:grid-cols-4`.

## Elevation & Depth

Sistem memakai bayangan lembar kertas yang disematkan — lembut, ber-offset, ber-blur — bukan flat murni dan bukan neobrutalis.

### Shadow Vocabulary
- **Lembar** (`0 1px 1px rgb(34 50 42/0.05), 0 2px 4px -1px rgb(34 50 42/0.06), 0 12px 32px -16px rgb(34 50 42/0.22)`): kartu, sheet, KPI — kedalaman istirahat lembaran yang disematkan.
- **Lembar Terangkat** (`0 1px 1px rgb(34 50 42/0.05), 0 8px 20px -8px rgb(34 50 42/0.16), 0 24px 48px -24px rgb(34 50 42/0.3)`): modal, dropdown, drawer, toast — lembar yang terangkat dari papan.
- **Pin** (`0 0 0 3px rgb(255 255 255/0.55), 0 2px 6px -1px rgb(18 64 47/0.5)`): lingkaran penanda pin (pin-dot).

### Named Rules
**The Sheet-Lift Rule.** Kedalaman selalu sebagai lembaran: bayangan ber-offset + ber-blur, menaik dari dasar datar. Tidak ada hard-offset shadow, tidak ada glow ber-halo nol-offset.

## Shapes

Sudut lembut namun terkendali: lembaran/kartu sedikit lebih bulat dari kontrol.

- **Kontrol** (0.625rem): tombol, input, select, field.
- **Lembar** (0.875rem): kartu, sheet, modal, dropdown, KPI.
- **Pin-dot** (999px): penanda status bulat sempurna dengan gloss inset.
- **Avatar** (999px): lingkaran inisial dengan latar `primary-soft` (atau `primary` untuk user di topbar).

## Components

### Buttons
- **Shape:** sudut kontrol (0.625rem), tinggi 32/40/48px (sm/md/lg), padding horizontal 12/16/20px, font semibold.
- **Primary:** hijau madrasah (#1b7a45) + teks putih; hover `primary-strong`; aksen `active:scale-[0.98]`.
- **Secondary:** lembar putih + ring garis; hover kertas pekat.
- **Outline:** transparan + ring hijau + teks hijau; hover latar `primary-soft`.
- **Danger / Success:** solid merah/hijau + teks putih.
- **Ghost:** transparan + teks `ink-soft`; hover kertas pekat.
- **Focus:** outline 2px `primary`, offset 2px (via `:focus-visible` global).

### Badges (Pin Status)
- **Style:** pil (999px), latar status-soft, teks status, ring 1px status/30, dengan pin-dot kecil di depan.
- **Variant:** neutral / success / warning / danger / info / primary. Selalu ada label teks; dot boleh disembunyikan (`:dot="false"`).

### Sheets (Kartu / Lembar)
- **Corner Style:** 0.875rem.
- **Background:** lembar putih (#ffffff).
- **Shadow:** var(--shadow-sheet).
- **Border:** ring 1px `rule/60`.
- **Internal Padding:** header `px-5 py-4 sm:px-6` dengan border-b `rule/70`; isi `px-5 py-5 sm:px-6`.
- **Ruled:** prop `ruled` menyalakan latar kertas bergaris halus (garis `rule` 55% tiap 32px, mulai 0.85em) — wajib untuk kartu daftar, KPI, dan form section agar terasa lembar administrasi.
- **Pinned:** prop `pinned` menambah pin-dot hijau di samping judul (lembar disematkan).

### KPI (Digit-Bank)
- Kartu lembar dengan pin-dot di pojok kanan atas; label `ink-soft` + ikon `ink-faint`; nilai JetBrains Mono 3xl bold `tabular`; tren naik hijau / turun merah dengan ikon panah.

### Inputs / Fields
- **Style:** lembar putih, ring 1px `rule-strong`, sudut 0.625rem, padding 10px 14px, teks 0.875rem.
- **Focus:** ring 2px `primary` (menggantikan outline). Hover ring `ink-faint/60`.
- **Error:** ring `danger/60`, focus ring `danger`; pesan error dengan ikon peringatan + teks `danger`.
- **Prefix:** teks `ink-faint` di kiri (mis. "+62").

### Navigation (Sidebar Papan)
- **Style:** latar papan (#12402f) dengan tekstur dot halus (`board-face`); header kop papan dengan logo + nama madrasah; kelompok label uppercase kecil `board-ink/60`; item teks `board-ink/70`, icon 18px.
- **Active:** latar `board-soft/40` + ring `board-soft/60` + pin-dot putih — item "tersemat" dan terangkat dari papan.
- **Hover:** `board-soft/30`.
- **Mobile:** drawer off-canvas 288px dengan backdrop `board-deep/60` blur; tutup via X di header.

### Table (Lembar Tabel)
- Header: label uppercase kecil `ink-soft`, border-b `rule-strong`; body `divide-y rule/70`; row hover `bg-paper/60`. Nomor NIS & nominal memakai mono tabular. Bungkus `overflow-x-auto` untuk mobile.

### Modal
- Backdrop `board-deep/60` + blur 2px; panel lembar putih 0.875rem, shadow raised; enter: opacity + translate-y + scale; tutup via X atau klik backdrop.

### Toast
- Panel `board` gelap + teks `board-ink`, pin-dot hijau, shadow raised, slide-in kanan bawah; auto-dismiss 4.2 detik (Alpine store).

### Signature: Pin/Unpin Approval
Tindakan utama daftar "Perlu Tindakan": tombol "Sematkan & Setujui" menandai lembar selesai — pin-dot hijau muncul dengan transisi scale-in, baris meredup dengan garis-coret, dan toast muncul ("Lembar P-0042 disetujui dan disematkan ke papan"). Ini satu-satunya momen gerak yang diorchestrasi di seluruh sistem.

## Do's and Don'ts

### Do:
- **Do** gunakan kertas hangat (#f6f1e7) sebagai tanah halaman dan lembar putih sebagai kartu.
- **Do** tampilkan status sebagai pin berwarna + label teks; angka data memakai JetBrains Mono tabular.
- **Do** beri teks pada setiap ikon (label tombol, badge, item nav).
- **Do** gunakan satu hijau primer per layar/kartu untuk satu tindakan utama.
- **Do** gunakan `ruled` pada kartu daftar/KPI/form agar terasa lembar administrasi.
- **Do** pakai Bahasa Indonesia untuk seluruh antarmuka.

### Don't:
- **Don't** tampilkan status hanya lewat warna — selalu sertakan label teks.
- **Don't** pakai hard-offset shadow atau glow ber-halo; kedalaman hanya lewat shadow lembar ber-blur.
- **Don't** gunakan emoji/glyph sebagai ikon; ikon dari set Heroicons outline stroke konsisten.
- **Don't** pakai font display sistem (Impact, Arial Black) untuk judul; gunakan Plus Jakarta Sans.
- **Don't** buat gradien teks; penekanan dari bobot dan ukuran.
- **Don't** gunakan kicker/eyebrow di atas judul; judul berdiri sendiri.
- **Don't** biarkan halaman meluap horizontal di mobile; tabel memakai scroll internal, pagination memakai flex-wrap.
