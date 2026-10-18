<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ErrorHandlingTest extends TestCase
{
    use RefreshDatabase;

    public function test_non_existent_url_renders_custom_404_page(): void
    {
        $response = $this->get('/halaman-ngawur-yang-pasti-tidak-ada-12345');

        $response->assertStatus(404);
        $response->assertSee('404');
        $response->assertSee('Halaman Tidak Ditemukan');
        $response->assertSee('Kembali ke Beranda');
        $response->assertDontSee('Whoops');
    }

    public function test_resident_cannot_access_admin_page_and_receives_custom_403_page(): void
    {
        $warga = User::factory()->create([
            'role' => 'warga',
            'status_verifikasi' => 'disetujui',
        ]);

        $response = $this->actingAs($warga)->get('/admin/warga');

        $response->assertStatus(403);
        $response->assertSee('403');
        $response->assertSee('Akses Tidak Diizinkan');
        $response->assertDontSee('Whoops');
    }

    public function test_unapproved_resident_is_redirected_to_menunggu_page(): void
    {
        $pendingUser = User::factory()->create([
            'role' => 'warga',
            'status_verifikasi' => 'pending',
        ]);

        $response = $this->actingAs($pendingUser)->get('/dashboard');

        $response->assertRedirect('/menunggu');
    }
}
