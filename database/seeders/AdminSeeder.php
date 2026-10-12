<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\User::create([
    'name' => 'Pengurus RT',
    'email' => 'admin@rt.test',
    'no_rumah' => '-',
    'no_wa' => '628000000000',
    'password' => bcrypt('password123'),
    'role' => 'admin',
    'status_verifikasi' => 'disetujui',
]);
    }
}
