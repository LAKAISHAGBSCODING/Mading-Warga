<?php

namespace Database\Seeders;

use App\Models\Acara;
use App\Models\ForumComment;
use App\Models\ForumPost;
use App\Models\Lapak;
use App\Models\Pengumuman;
use App\Models\PengumumanDibaca;
use App\Models\Rsvp;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class MadingDataSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::updateOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name' => 'Pengurus RT',
                'password' => Hash::make('warga123'),
                'role' => 'admin',
                'status_verifikasi' => 'disetujui',
                'no_rumah' => '-',
                'no_wa' => '628000000000',
                'foto' => null,
            ]
        );

        $warga = [];
        $dataWarga = [
            'budi@gmail.com' => ['Budi Santoso', 'B-01', '628123456780'],
            'siti@gmail.com' => ['Siti Aminah', 'A-02', '628123456781'],
            'andi@gmail.com' => ['Andi Pratama', 'A-03', '628123456782'],
            'dewi@gmail.com' => ['Dewi Lestari', 'A-04', '628123456783'],
            'rina@gmail.com' => ['Rina Kusuma', 'B-05', '628123456784'],
            'joko@gmail.com' => ['Joko Widodo', 'B-06', '628123456785'],
            'maya@gmail.com' => ['Maya Putri', 'C-07', '628123456786'],
            'agus@gmail.com' => ['Agus Saputra', 'C-08', '628123456787'],
        ];

        foreach ($dataWarga as $email => [$nama, $noRumah, $noWa]) {
            $warga[$email] = User::updateOrCreate(
                ['email' => $email],
                [
                    'name' => $nama,
                    'password' => Hash::make('warga123'),
                    'role' => 'warga',
                    'status_verifikasi' => 'disetujui',
                    'no_rumah' => $noRumah,
                    'no_wa' => $noWa,
                ]
            );
        }

        $pending = User::updateOrCreate(
            ['email' => 'adi.pending@gmail.com'],
            [
                'name' => 'Adi Pratama',
                'password' => Hash::make('warga123'),
                'role' => 'warga',
                'status_verifikasi' => 'pending',
                'no_rumah' => 'C-09',
                'no_wa' => '628123456788',
            ]
        );

        $pengumumanData = [
            ['judul' => 'Kerja Bakti dan Pembersihan Selokan', 'isi' => 'Kerja bakti lingkungan dilaksanakan Minggu pukul 07.00 WIB. Warga dimohon membawa alat kebersihan dan berkumpul di pos RT.', 'pin' => true, 'hari' => 1],
            ['judul' => 'Jadwal Ronda Malam Bulan Ini', 'isi' => 'Jadwal ronda terbaru sudah dibagikan per blok. Silakan periksa giliran rumah masing-masing dan hubungi pengurus bila perlu bertukar jadwal.', 'pin' => true, 'hari' => 2],
            ['judul' => 'Pembayaran Iuran Kebersihan', 'isi' => 'Iuran kebersihan bulan ini sebesar Rp50.000 per rumah. Pembayaran dapat diserahkan kepada bendahara paling lambat tanggal 20.', 'pin' => false, 'hari' => 3],
            ['judul' => 'Pemadaman Air Sementara', 'isi' => 'Akan ada perbaikan pipa utama pada Selasa pukul 09.00–12.00 WIB. Warga dimohon menyiapkan persediaan air secukupnya.', 'pin' => false, 'hari' => 4],
            ['judul' => 'Posyandu Balita dan Lansia', 'isi' => 'Pelayanan posyandu dilaksanakan Sabtu pukul 08.00 WIB di balai warga. Mohon membawa buku kesehatan masing-masing.', 'pin' => false, 'hari' => 5],
            ['judul' => 'Pendataan Kendaraan Warga', 'isi' => 'Pengurus sedang memperbarui data kendaraan untuk keamanan lingkungan. Kirimkan nomor polisi kendaraan kepada ketua RT.', 'pin' => false, 'hari' => 6],
        ];

        $pengumumans = [];
        foreach ($pengumumanData as $data) {
            $tanggal = now()->subDays($data['hari']);
            $pengumumans[$data['judul']] = Pengumuman::updateOrCreate(
                ['user_id' => $admin->id, 'judul' => $data['judul']],
                [
                    'isi' => $data['isi'],
                    'lampiran' => null,
                    'is_pinned' => $data['pin'],
                    'pinned_at' => $data['pin'] ? $tanggal : null,
                    'created_at' => $tanggal,
                    'updated_at' => $tanggal,
                ]
            );
        }

        foreach ([$warga['budi@gmail.com'], $warga['siti@gmail.com'], $warga['andi@gmail.com']] as $pembaca) {
            PengumumanDibaca::firstOrCreate([
                'pengumuman_id' => $pengumumans['Kerja Bakti dan Pembersihan Selokan']->id,
                'user_id' => $pembaca->id,
            ]);
        }

        $acaraData = [
            [
                'judul' => 'Rapat Rutin Warga',
                'deskripsi' => 'Membahas laporan kas, keamanan lingkungan, dan rencana kegiatan bulan depan.',
                'tanggal' => today()->addDays(3),
                'jam' => '19:30',
                'lokasi' => 'Balai Warga',
                'kuota' => 20,
                'status' => 'disetujui',
                'pemilik' => 'admin',
                'alasan_penolakan' => null,
            ],
            [
                'judul' => 'Kerja Bakti Taman dan Selokan',
                'deskripsi' => 'Membersihkan taman bersama dan mengangkat sampah di saluran air.',
                'tanggal' => today()->addDays(6),
                'jam' => '07:00',
                'lokasi' => 'Taman RT',
                'kuota' => 12,
                'status' => 'disetujui',
                'pemilik' => 'admin',
                'alasan_penolakan' => null,
            ],
            [
                'judul' => 'Senam Sehat Minggu Pagi',
                'deskripsi' => 'Senam bersama terbuka untuk semua usia. Tidak ada batas jumlah peserta.',
                'tanggal' => today()->addDays(9),
                'jam' => '06:30',
                'lokasi' => 'Lapangan RT',
                'kuota' => null,
                'status' => 'disetujui',
                'pemilik' => 'admin',
                'alasan_penolakan' => null,
            ],
            [
                'judul' => 'Usulan Kelas Mengaji Anak',
                'deskripsi' => 'Usulan kelas mengaji rutin setiap Sabtu sore untuk anak-anak di lingkungan RT.',
                'tanggal' => today()->addDays(12),
                'jam' => '16:00',
                'lokasi' => 'Mushola Al-Ikhlas',
                'kuota' => 15,
                'status' => 'menunggu',
                'pemilik' => 'budi@gmail.com',
                'alasan_penolakan' => null,
            ],
            [
                'judul' => 'Usulan Penutupan Jalan untuk Bazar',
                'deskripsi' => 'Usulan bazar malam warga pada akhir pekan.',
                'tanggal' => today()->addDays(15),
                'jam' => '18:00',
                'lokasi' => 'Jalan Utama RT',
                'kuota' => 50,
                'status' => 'ditolak',
                'pemilik' => 'siti@gmail.com',
                'alasan_penolakan' => 'Jalan utama harus tetap terbuka untuk akses kendaraan darurat.',
            ],
        ];

        $acaras = [];
        foreach ($acaraData as $data) {
            $pemilik = $data['pemilik'] === 'admin' ? $admin : $warga[$data['pemilik']];
            $acaras[$data['judul']] = Acara::updateOrCreate(
                ['user_id' => $pemilik->id, 'judul' => $data['judul']],
                [
                    'deskripsi' => $data['deskripsi'],
                    'tanggal' => $data['tanggal'],
                    'jam' => $data['jam'],
                    'lokasi' => $data['lokasi'],
                    'kuota' => $data['kuota'],
                    'status' => $data['status'],
                    'alasan_penolakan' => $data['alasan_penolakan'],
                ]
            );
        }

        $dataRsvp = [
            'Rapat Rutin Warga' => [
                'budi@gmail.com' => 'hadir',
                'siti@gmail.com' => 'hadir',
                'andi@gmail.com' => 'tidak_hadir',
                'dewi@gmail.com' => 'hadir',
            ],
            'Kerja Bakti Taman dan Selokan' => [
                'budi@gmail.com' => 'hadir',
                'rina@gmail.com' => 'hadir',
                'joko@gmail.com' => 'hadir',
                'maya@gmail.com' => 'tidak_hadir',
            ],
            'Senam Sehat Minggu Pagi' => [
                'agus@gmail.com' => 'hadir',
                'dewi@gmail.com' => 'hadir',
                'siti@gmail.com' => 'tidak_hadir',
            ],
        ];

        foreach ($dataRsvp as $judulAcara => $rsvps) {
            foreach ($rsvps as $email => $status) {
                Rsvp::updateOrCreate(
                    ['acara_id' => $acaras[$judulAcara]->id, 'user_id' => $warga[$email]->id],
                    ['status' => $status]
                );
            }
        }

        $postData = [
            ['pemilik' => 'budi@gmail.com', 'label' => 'Tanya Jawab', 'judul' => 'Rekomendasi tukang servis pompa air', 'isi' => 'Pompa air di rumah saya mulai bermasalah. Ada warga yang punya rekomendasi jasa servis tepercaya?'],
            ['pemilik' => 'siti@gmail.com', 'label' => 'Info', 'judul' => 'Jadwal posyandu bulan ini', 'isi' => 'Posyandu bulan ini diadakan Sabtu pagi di balai warga. Silakan membawa buku kesehatan anak.'],
            ['pemilik' => 'andi@gmail.com', 'label' => 'Pinjam Barang', 'judul' => 'Meminjam tangga lipat', 'isi' => 'Apakah ada warga yang bisa meminjamkan tangga lipat untuk hari Minggu? Akan saya kembalikan pada sore hari.'],
        ];

        foreach ($postData as $index => $data) {
            $post = ForumPost::updateOrCreate(
                ['user_id' => $warga[$data['pemilik']]->id, 'judul' => $data['judul']],
                ['label' => $data['label'], 'isi' => $data['isi']]
            );

            $penulisKomentar = $warga[$index === 0 ? 'siti@gmail.com' : 'budi@gmail.com'];
            ForumComment::firstOrCreate(
                [
                    'forum_post_id' => $post->id,
                    'user_id' => $penulisKomentar->id,
                    'isi' => $index === 0
                        ? 'Coba hubungi Pak Rudi di blok B, beliau biasa memperbaiki pompa.'
                        : 'Terima kasih infonya, saya catat.',
                ]
            );
        }

        $lapakData = [
            ['pemilik' => 'budi@gmail.com', 'nama' => 'Sayur Segar Pak Budi', 'harga' => 15000, 'deskripsi' => 'Paket sayur segar untuk masak sehari-hari.', 'tersedia' => true],
            ['pemilik' => 'siti@gmail.com', 'nama' => 'Kue Basah Bu Siti', 'harga' => 25000, 'deskripsi' => 'Aneka kue basah dibuat setiap pagi, bisa pesan satu hari sebelumnya.', 'tersedia' => true],
            ['pemilik' => 'andi@gmail.com', 'nama' => 'Jasa Servis Elektronik', 'harga' => 50000, 'deskripsi' => 'Servis kipas angin, setrika, dan peralatan rumah tangga.', 'tersedia' => true],
            ['pemilik' => 'dewi@gmail.com', 'nama' => 'Laundry Kiloan', 'harga' => 8000, 'deskripsi' => 'Cuci dan setrika, selesai dalam dua hari.', 'tersedia' => true],
            ['pemilik' => 'rina@gmail.com', 'nama' => 'Tanaman Hias Rina', 'harga' => 20000, 'deskripsi' => 'Tanaman hias rumahan dan media tanam.', 'tersedia' => false],
            ['pemilik' => 'joko@gmail.com', 'nama' => 'Jasa Pangkas Rambut', 'harga' => 15000, 'deskripsi' => 'Pangkas rambut di rumah dengan janji terlebih dahulu.', 'tersedia' => true],
        ];

        foreach ($lapakData as $data) {
            Lapak::updateOrCreate(
                ['user_id' => $warga[$data['pemilik']]->id, 'nama' => $data['nama']],
                [
                    'harga' => $data['harga'],
                    'deskripsi' => $data['deskripsi'],
                    'foto' => null,
                    'tersedia' => $data['tersedia'],
                ]
            );
        }

        $this->command?->info('Data demo Mading Warga berhasil disiapkan.');
        $this->command?->line('Akun warga pending: '.$pending->email.' (password: warga123).');
    }
}
