# Alur Demo Presentasi

## Persiapan

1. Jalankan `php artisan migrate:fresh --seed` pada database demo lokal.
2. Pastikan `php artisan storage:link` sudah dibuat dan aplikasi berjalan dengan `php artisan serve`.
3. Gunakan dua browser/profil agar akun admin dan warga dapat tetap masuk secara bergantian:
   - Admin: `admin@gmail.com` / `warga123`
   - Warga: `budi@gmail.com` / `warga123`

## Skenario presentasi

1. **Daftar warga baru.** Keluar dari sesi, buka halaman registrasi, lalu daftarkan akun warga uji dengan nama, email, dan kata sandi.
2. **Verifikasi oleh admin.** Masuk sebagai admin, buka menu verifikasi warga, lalu setujui akun yang baru didaftarkan.
3. **Buat dan sematkan pengumuman.** Dari menu Mading, buat pengumuman penting. Sematkan pengumuman agar terlihat di bagian atas daftar dan beranda.
4. **Warga membaca pengumuman.** Masuk sebagai Budi atau akun warga uji, buka pengumuman yang disematkan. Kembali ke daftar; penanda “Baru” sudah hilang.
5. **Periksa pembacaan.** Masuk kembali sebagai admin dan buka detail pengumuman untuk melihat jumlah warga yang membaca serta daftar warga yang sudah/belum membaca.
6. **Warga mengusulkan acara.** Sebagai warga, buka Agenda dan ajukan kegiatan dengan kuota peserta. Usulan akan menunggu persetujuan.
7. **Admin meninjau usulan.** Sebagai admin, buka agenda menunggu. Edit usulan dan pilih status disetujui untuk memublikasikannya. Ulangi dengan usulan lain dan pilih ditolak beserta alasan untuk menunjukkan notifikasi dan alasan penolakan kepada pengusul.
8. **RSVP dan kuota.** Buka acara yang disetujui sebagai beberapa akun warga. Kirim RSVP hadir sampai kuota terpenuhi; tombol hadir kemudian nonaktif dan permintaan hadir baru menerima pesan bahwa kuota penuh. RSVP tidak hadir tetap dapat dikirim.
9. **Unduh CSV.** Sebagai admin, gunakan tombol “Unduh daftar RSVP (CSV)” pada kartu acara dan tunjukkan kolom nama, nomor rumah, WhatsApp, status, serta waktu konfirmasi.
10. **Halaman error kustom.** Buka URL yang tidak tersedia, misalnya `/halaman-yang-tidak-ada`, dan tunjukkan halaman 404 berbahasa Indonesia dengan tautan kembali ke beranda.

## Pertanyaan dosen dan jawaban singkat

1. **Apa itu MVC pada aplikasi ini?** Model mengelola data dan relasi, View Blade menyajikan UI, dan Controller memproses request serta mengatur alur aplikasi.
2. **Bagaimana middleware digunakan?** Route memakai middleware `auth`, `approved`, dan `admin` untuk membatasi sesi, status verifikasi, serta hak akses pengurus.
3. **Apa fungsi migration dan seeder?** Migration mengubah struktur database secara terulang dan terlacak; seeder mengisi akun serta data demo secara idempoten.
4. **Relasi Eloquent apa yang dipakai?** Contohnya warga memiliki banyak acara, RSVP, dan postingan; acara memiliki banyak RSVP; postingan memiliki banyak komentar.
5. **Mengapa tabel pembacaan memakai UUID?** `pengumuman_dibacas` adalah tabel baru dan mengikuti konvensi proyek untuk menggunakan primary key UUID; foreign key ke tabel lama tetap bigint.
6. **Bagaimana aplikasi mencegah CSRF?** Semua form perubahan data menyertakan `@csrf`, lalu Laravel memeriksa token sesi pada request.
7. **Bagaimana validasi input dilakukan?** Controller memvalidasi input di server, menggunakan pesan dan nama atribut berbahasa Indonesia sebelum data disimpan.
8. **Bagaimana error ditangani?** Bootstrap mendaftarkan halaman kustom untuk error HTTP dan gangguan server; detail teknis dicatat di log, bukan ditampilkan ke pengguna.
9. **Mengapa memilih PostgreSQL?** PostgreSQL menyediakan constraint dan transaksi yang kuat, termasuk penguncian baris yang dipakai untuk menjaga kuota RSVP saat request bersamaan.
10. **Bagaimana hak akses edit/hapus dijaga?** Route membatasi fitur admin dengan middleware; aksi milik warga memeriksa pemilik record di server, bukan hanya menyembunyikan tombol UI.
