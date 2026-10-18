<?php

namespace Tests\Feature;

use App\Models\Acara;
use App\Models\ForumComment;
use App\Models\ForumPost;
use App\Models\Lapak;
use App\Models\Pengumuman;
use App\Models\User;
use Database\Seeders\MadingDataSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class MadingDataSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_demo_seeder_creates_required_data_and_is_idempotent(): void
    {
        $this->seed(MadingDataSeeder::class);

        $this->assertSame(8, User::where('role', 'warga')->where('status_verifikasi', 'disetujui')->count());
        $this->assertSame(1, User::where('role', 'warga')->where('status_verifikasi', 'pending')->count());
        $this->assertSame(6, Pengumuman::count());
        $this->assertSame(2, Pengumuman::where('is_pinned', true)->count());
        $this->assertSame(5, Acara::count());
        $this->assertGreaterThanOrEqual(3, ForumPost::count());
        $this->assertGreaterThanOrEqual(3, ForumComment::count());
        $this->assertSame(6, Lapak::count());

        $this->seed(MadingDataSeeder::class);

        $this->assertSame(8, User::where('role', 'warga')->where('status_verifikasi', 'disetujui')->count());
        $this->assertSame(1, User::where('role', 'warga')->where('status_verifikasi', 'pending')->count());
        $this->assertSame(6, Pengumuman::count());
        $this->assertSame(5, Acara::count());
        $this->assertSame(3, ForumPost::count());
        $this->assertSame(3, ForumComment::count());
        $this->assertSame(6, Lapak::count());

        $admin = User::where('email', 'admin@gmail.com')->firstOrFail();
        $budi = User::where('email', 'budi@gmail.com')->firstOrFail();
        $this->assertSame('admin', $admin->role);
        $this->assertSame('warga', $budi->role);
        $this->assertTrue(Hash::check('warga123', $admin->password));
        $this->assertTrue(Hash::check('warga123', $budi->password));
    }
}
