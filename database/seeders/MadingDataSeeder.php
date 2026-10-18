<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class MadingDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Akun Admin / Pengurus RT Resmi (berdasarkan email agar sequence aman)
        DB::table('users')->updateOrInsert(
            ['email' => 'admin@gmail.com'],
            [
                'name'              => 'Pengurus RT',
                'password'          => Hash::make('warga123'),
                'role'              => 'admin',
                'status_verifikasi' => 'disetujui',
                'no_rumah'          => '-',
                'no_wa'             => '628000000000',
                'foto'              => null,
                'created_at'        => now(),
                'updated_at'        => now(),
            ]
        );

        // Ambil ID admin yang tersimpan
        $adminId = DB::table('users')->where('email', 'admin@gmail.com')->value('id');

        // 2. Pengumuman Resmi dari Pengurus RT
        $pengumumans = [
            [
                'id'         => 1,
                'user_id'    => $adminId,
                'judul'      => 'Kerja Bakti Bulanan Lingkungan RT',
                'isi'        => 'Diberitahukan kepada seluruh warga bahwa kerja bakti bulanan akan dilaksanakan hari Minggu pagi pukul 07.00 WIB. Mohon membawa peralatan kebersihan seperti sapu, cangkul, dan kantong sampah.',
                'lampiran'   => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id'         => 2,
                'user_id'    => $adminId,
                'judul'      => 'Iuran Keamanan & Kebersihan Bulan Ini',
                'isi'        => 'Iuran bulanan sebesar Rp 50.000 per rumah tangga. Pembayaran dapat diserahkan langsung ke bendahara RT paling lambat tanggal 20.',
                'lampiran'   => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($pengumumans as $item) {
            DB::table('pengumumans')->updateOrInsert(['id' => $item['id']], $item);
        }

        // 3. Agenda / Acara Resmi RT
        $acaras = [
            [
                'id'         => 1,
                'user_id'    => $adminId,
                'judul'      => 'Rapat Rutin Pengurus & Warga',
                'deskripsi'  => 'Pembahasan laporan keuangan, keamanan ronda malam, dan rencana perbaikan fasilitas umum.',
                'tanggal'    => '2026-10-25',
                'jam'        => '19:30:00',
                'lokasi'     => 'Balai Warga RT',
                'status'     => 'disetujui',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($acaras as $item) {
            DB::table('acaras')->updateOrInsert(['id' => $item['id']], $item);
        }

        // 4. Sinkronisasi nomor urut ID PostgreSQL agar tidak error saat registrasi
        if (config('database.default') === 'pgsql') {
            DB::statement("SELECT setval('users_id_seq', coalesce((SELECT MAX(id) FROM users), 1));");
            DB::statement("SELECT setval('pengumumans_id_seq', coalesce((SELECT MAX(id) FROM pengumumans), 1));");
            DB::statement("SELECT setval('acaras_id_seq', coalesce((SELECT MAX(id) FROM acaras), 1));");
        }
    }
}