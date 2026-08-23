# Tahap 8 — Instalasi Database dan Laravel

> Arsip hasil percakapan AI untuk proyek SIM Madrasah.
> Sumber asli: `SIM-MADRASAH-AI-HISTORY.md`, baris 10990–11205.
> File ini adalah arsip historis. Untuk melanjutkan proyek, baca `docs/AI-HANDOFF.md` terlebih dahulu.

---

=========================================================
Tahap 8 — Instalasi Database dan Membuat Proyek Laravel
Untuk proyek SIM Madrasah, gunakan MySQL 8.4 LTS. Jangan memasang MySQL dan MariaDB sekaligus karena keduanya memakai layanan dan port database yang sama. MySQL 8.4 merupakan jalur LTS, sedangkan perintah brew install mysql saat ini memasang seri MySQL 9.x; karena itu kita menggunakan formula versi khusus mysql@8.4. (MySQL Developer Zone)
Herd versi gratis sudah menyediakan ekstensi PHP untuk MySQL dan MariaDB, tetapi layanan databasenya perlu dipasang terpisah. (Laravel Herd)

A. Memeriksa Homebrew
Buka Terminal, lalu jalankan:
brew --version
Bila muncul versi Homebrew
Contohnya:
Homebrew 5.x.x
Langsung lanjut ke bagian B.
Bila muncul command not found
Pasang Homebrew:
/bin/bash -c "$(curl -fsSL https://raw.githubusercontent.com/Homebrew/install/HEAD/install.sh)"
Masukkan password MacBook apabila diminta. Saat password diketik, karakter memang tidak terlihat.
Setelah instalasi, Terminal biasanya menampilkan bagian Next steps. Salin dan jalankan perintah yang diberikan di bagian tersebut agar Homebrew masuk ke PATH. Lokasi standar Homebrew adalah /opt/homebrew pada Apple Silicon dan /usr/local pada Mac Intel. (Homebrew Documentation)
Tutup Terminal, buka kembali, lalu periksa:
brew --version

B. Memasang MySQL 8.4
Jalankan:
brew update
brew install mysql@8.4
Tunggu sampai selesai. Homebrew menyediakan formula resmi mysql@8.4 untuk Mac Apple Silicon maupun Intel. (Homebrew Formulae)
Karena formula tersebut bersifat keg-only, masukkan perintah MySQL ke PATH:
echo 'export PATH="$(brew --prefix mysql@8.4)/bin:$PATH"' >> ~/.zshrc
source ~/.zshrc
Periksa versi:
mysql --version
Targetnya kurang lebih:
mysql  Ver 8.4.x

C. Menjalankan layanan MySQL
Jalankan MySQL sebagai layanan macOS:
brew services start mysql@8.4
Periksa statusnya:
brew services list
Pada baris mysql@8.4, statusnya harus:
started
Perintah pengelolaannya nanti:
brew services stop mysql@8.4
brew services restart mysql@8.4
brew services start mysql@8.4

D. Mengamankan instalasi MySQL
Instalasi Homebrew biasanya membuat root lokal tanpa password awal. Jalankan:
mysql_secure_installation
Program ini membantu mengatur password root, menghapus pengguna anonim, menonaktifkan root jarak jauh, dan menghapus database pengujian. (MySQL Developer Zone)
Jawaban yang disarankan:
Enter password for user root:
Tekan Enter karena password awal masih kosong.
Untuk pertanyaan selanjutnya:
Set root password?                         Y
Remove anonymous users?                   Y
Disallow root login remotely?             Y
Remove test database and access to it?    Y
Reload privilege tables now?              Y
Buat password root yang kuat dan simpan sendiri. Jangan kirimkan password tersebut melalui screenshot.

E. Membuat database dan pengguna aplikasi
Masuk ke MySQL:
mysql -u root -p
Masukkan password root yang baru dibuat.
Setelah muncul prompt:
mysql>
jalankan perintah berikut satu per satu:
CREATE DATABASE sim_madrasah
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;
Buat pengguna khusus aplikasi:
CREATE USER 'sim_madrasah_user'@'127.0.0.1'
IDENTIFIED BY 'GANTI_DENGAN_PASSWORD_LOKAL_YANG_KUAT';
Berikan akses hanya ke database SIM Madrasah:
GRANT ALL PRIVILEGES ON sim_madrasah.*
TO 'sim_madrasah_user'@'127.0.0.1';
Kemudian:
FLUSH PRIVILEGES;
Periksa hasilnya:
SHOW DATABASES;
Harus terlihat:
sim_madrasah
Keluar:
EXIT;
Gunakan password yang sama nanti di file .env.
Uji pengguna aplikasi
mysql -h 127.0.0.1 -u sim_madrasah_user -p sim_madrasah
Masukkan password pengguna aplikasi.
Jika berhasil masuk dan terlihat prompt mysql>, jalankan:
SELECT DATABASE();
Hasilnya harus menunjukkan:
sim_madrasah
Kemudian keluar:
EXIT;

F. Membuat proyek Laravel 12
Masuk ke folder Herd:
cd ~/Herd
Pastikan belum ada folder dengan nama yang sama:
ls
Buat proyek Laravel 12:
composer create-project laravel/laravel sim-madrasah "^12.0"
Perintah ini sengaja mengunci proyek pada seri Laravel 12, bukan otomatis mengikuti Laravel terbaru.
Tunggu sampai Composer selesai, kemudian masuk ke folder proyek:
cd ~/Herd/sim-madrasah
Periksa versi Laravel:
php artisan --version
Target hasil:
Laravel Framework 12.x.x
Laravel resmi mendukung pembuatan aplikasi melalui Composer/Laravel Installer dan menggunakan Node serta NPM untuk membangun aset frontend. (Laravel)

G. Mengatur file .env
Masih di folder:
~/Herd/sim-madrasah
Buka .env:
nano .env
Cari dan ubah bagian aplikasi menjadi:
APP_NAME="SIM Madrasah"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://sim-madrasah.test
Cari bagian database, lalu ubah menjadi:
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=sim_madrasah
DB_USERNAME=sim_madrasah_user
DB_PASSWORD="GANTI_DENGAN_PASSWORD_LOKAL_YANG_KUAT"
Password harus sama dengan password yang digunakan saat menjalankan CREATE USER.
Simpan di Nano:
1. Tekan Control + O
2. Tekan Enter
3. Tekan Control + X
Jangan menggunakan localhost untuk saat ini. Gunakan:
DB_HOST=127.0.0.1
agar Laravel memakai koneksi TCP yang sama dengan pengguna MySQL yang sudah dibuat.

H. Membuat application key dan menguji database
Jalankan:
php artisan key:generate
Bersihkan cache konfigurasi:
php artisan config:clear
Uji koneksi database dengan migration:
php artisan migrate
Hasil yang benar akan menampilkan sejumlah migration dengan status:
DONE
Periksa tabel:
php artisan migrate:status
Semua migration awal harus berstatus:
Ran
Bila muncul:
Access denied for user
berarti username atau password pada .env tidak sama dengan pengguna MySQL.
Bila muncul:
Connection refused
periksa layanan:
brew services list
Lalu restart:
brew services restart mysql@8.4

I. Memasang aset frontend
Jalankan dari folder proyek:
npm install
Setelah selesai:
npm run build
Laravel menggunakan Node/NPM untuk membangun aset frontend, dan dokumentasi Laravel menyarankan menjalankan instalasi serta build aset setelah proyek dibuat. (Laravel)
Untuk pengembangan nanti, Vite dapat dijalankan dengan:
npm run dev
Terminal tersebut harus tetap terbuka selama mode pengembangan aktif. Untuk pemeriksaan awal, npm run build sudah cukup.

J. Membuka aplikasi
Karena proyek berada di folder default Herd:
~/Herd/sim-madrasah
Herd otomatis menyediakannya melalui domain .test. (Laravel Herd)
Buka dari Terminal:
open http://sim-madrasah.test
Atau ketik di browser:
http://sim-madrasah.test
Halaman awal Laravel harus muncul.

K. Pemeriksaan akhir Tahap 8
Jalankan:
php artisan about
Kemudian:
php artisan migrate:status
Checklist yang harus terpenuhi:
PHP                8.4.x
Laravel            12.x
Environment        local
Debug mode         enabled
Database           mysql
MySQL service      started
Migration          seluruhnya Ran
Node               22.x
NPM                tersedia
Website            sim-madrasah.test dapat dibuka
Pada tahap ini kita belum memasang Laravel Breeze. Authentication, login, lupa password, serta pengaturan awal pengguna akan dikerjakan pada Tahap 9: Authentication setelah instalasi dasar ini berhasil.

Alternatif MariaDB
Bagian ini hanya digunakan bila Anda memutuskan tidak memakai MySQL. Jangan menjalankan bagian ini setelah MySQL terpasang. MariaDB resmi mendukung instalasi macOS melalui Homebrew. (MariaDB)
Gunakan versi MariaDB terkontrol:
brew install mariadb@11.4
Tambahkan ke PATH:
echo 'export PATH="$(brew --prefix mariadb@11.4)/bin:$PATH"' >> ~/.zshrc
source ~/.zshrc
Jalankan layanan:
brew services start mariadb@11.4
Amankan instalasi:
mariadb-secure-installation
MariaDB menyediakan utilitas tersebut untuk menghapus akun anonim, membatasi root, dan menghapus database pengujian. (MariaDB)
Untuk proyek ini, keputusan utama tetap:
Gunakan MySQL 8.4 dan abaikan bagian MariaDB.
Kerjakan sampai perintah berikut berhasil:
brew services list
mysql --version
Lalu lanjutkan pembuatan database dan proyek sesuai urutan di atas.
