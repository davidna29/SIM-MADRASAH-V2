---
paths:
  - 'resources/views/**'
  - 'resources/views/**/*.blade.php'
---

# Views

## Beri spasi di antara direktif Blade yang berurutan
Jangan menulis dua direktif Blade berdempetan tanpa spasi (mis. @endif@if atau teks diakhiri kata langsung disambung @if/@endif) — regex Blade memakai \B sehingga direktif setelah karakter kata tidak terkompilasi dan menghasilkan ParseError "unexpected endif" saat render. Selalu beri spasi/newline di antara direktif.

## Wajib rebuild + commit public/build tiap ubah kelas Blade
Build Tailwind bersifat statis: setiap menambah/mengubah kelas di Blade WAJIB jalankan npm run build lalu commit ulang public/build. CSS basi = utilitas baru hilang diam-diam (pernah: gradien KPI & chart tak ter-render). Verifikasi dengan grep kelas baru di public/build/assets/*.css.
