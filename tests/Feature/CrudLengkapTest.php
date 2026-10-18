<?php

namespace Tests\Feature;

use App\Models\Acara;
use App\Models\ForumPost;
use App\Models\Lapak;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CrudLengkapTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_can_edit_and_update_their_lapak(): void
    {
        $owner = User::factory()->create(['role' => 'warga', 'status_verifikasi' => 'disetujui']);
        $lapak = Lapak::create([
            'user_id' => $owner->id,
            'nama' => 'Jual Madu Asli',
            'harga' => 50000,
            'deskripsi' => 'Madu murni dari hutan',
            'tersedia' => true,
        ]);

        $response = $this->actingAs($owner)->get(route('lapak.edit', $lapak));
        $response->assertOk();
        $response->assertSee('Ubah lapak');

        $updateResponse = $this->actingAs($owner)->put(route('lapak.update', $lapak), [
            'nama' => 'Jual Madu Asli Hutan Super',
            'harga' => 60000,
            'deskripsi' => 'Madu murni kualitas super',
        ]);

        $updateResponse->assertRedirect(route('lapak.index'));
        $this->assertSame('Jual Madu Asli Hutan Super', $lapak->fresh()->nama);
        $this->assertSame(60000, $lapak->fresh()->harga);
    }

    public function test_non_owner_cannot_edit_other_users_lapak(): void
    {
        $owner = User::factory()->create(['role' => 'warga', 'status_verifikasi' => 'disetujui']);
        $other = User::factory()->create(['role' => 'warga', 'status_verifikasi' => 'disetujui']);
        $lapak = Lapak::create([
            'user_id' => $owner->id,
            'nama' => 'Jual Madu Asli',
            'harga' => 50000,
            'deskripsi' => 'Madu murni',
            'tersedia' => true,
        ]);

        $this->actingAs($other)->get(route('lapak.edit', $lapak))->assertStatus(403);
        $this->actingAs($other)->put(route('lapak.update', $lapak), [
            'nama' => 'Hacked',
            'harga' => 1000,
            'deskripsi' => 'Hacked',
        ])->assertStatus(403);
    }

    public function test_admin_can_edit_any_lapak(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $owner = User::factory()->create(['role' => 'warga', 'status_verifikasi' => 'disetujui']);
        $lapak = Lapak::create([
            'user_id' => $owner->id,
            'nama' => 'Jual Madu Asli',
            'harga' => 50000,
            'deskripsi' => 'Madu murni',
            'tersedia' => true,
        ]);

        $response = $this->actingAs($admin)->put(route('lapak.update', $lapak), [
            'nama' => 'Jual Madu Resmi RT',
            'harga' => 55000,
            'deskripsi' => 'Diverifikasi pengurus',
        ]);

        $response->assertRedirect(route('lapak.index'));
        $this->assertSame('Jual Madu Resmi RT', $lapak->fresh()->nama);
    }

    public function test_owner_can_edit_and_update_forum_post(): void
    {
        $owner = User::factory()->create(['role' => 'warga', 'status_verifikasi' => 'disetujui']);
        $post = ForumPost::create([
            'user_id' => $owner->id,
            'label' => 'Tanya Jawab',
            'judul' => 'Ada yang punya tangga lipat?',
            'isi' => 'Mau pinjam untuk benerin genteng.',
        ]);

        $response = $this->actingAs($owner)->get(route('forum.edit', $post));
        $response->assertOk();
        $response->assertSee('Ubah postingan');

        $updateResponse = $this->actingAs($owner)->put(route('forum.update', $post), [
            'label' => 'Pinjam Barang',
            'judul' => 'Pinjam tangga lipat 3 meter',
            'isi' => 'Mau pinjam hari Minggu ini.',
        ]);

        $updateResponse->assertRedirect(route('forum.show', $post));
        $this->assertSame('Pinjam Barang', $post->fresh()->label);
        $this->assertSame('Pinjam tangga lipat 3 meter', $post->fresh()->judul);
    }

    public function test_non_owner_cannot_edit_other_users_forum_post(): void
    {
        $owner = User::factory()->create(['role' => 'warga', 'status_verifikasi' => 'disetujui']);
        $other = User::factory()->create(['role' => 'warga', 'status_verifikasi' => 'disetujui']);
        $post = ForumPost::create([
            'user_id' => $owner->id,
            'label' => 'Info',
            'judul' => 'Info posyandu',
            'isi' => 'Jadwal posyandu besok pagi.',
        ]);

        $this->actingAs($other)->get(route('forum.edit', $post))->assertStatus(403);
    }

    public function test_resident_can_edit_and_delete_their_pending_event_proposal(): void
    {
        $warga = User::factory()->create(['role' => 'warga', 'status_verifikasi' => 'disetujui']);
        $acara = Acara::create([
            'user_id' => $warga->id,
            'judul' => 'Turnamen Catur Warga',
            'tanggal' => today()->addDays(5),
            'jam' => '20:00',
            'lokasi' => 'Pos Ronda 1',
            'status' => 'menunggu',
        ]);

        $response = $this->actingAs($warga)->get(route('acara.edit', $acara));
        $response->assertOk();

        $updateResponse = $this->actingAs($warga)->put(route('acara.update', $acara), [
            'judul' => 'Turnamen Catur & Karambol Warga',
            'tanggal' => today()->addDays(6)->toDateString(),
            'jam' => '19:30',
            'lokasi' => 'Balai Warga',
        ]);

        $updateResponse->assertRedirect(route('acara.index'));
        $this->assertSame('Turnamen Catur & Karambol Warga', $acara->fresh()->judul);

        $deleteResponse = $this->actingAs($warga)->delete(route('acara.destroy', $acara));
        $deleteResponse->assertRedirect(route('acara.index'));
        $this->assertNull(Acara::find($acara->id));
    }

    public function test_resident_cannot_edit_already_approved_event(): void
    {
        $warga = User::factory()->create(['role' => 'warga', 'status_verifikasi' => 'disetujui']);
        $acara = Acara::create([
            'user_id' => $warga->id,
            'judul' => 'Jalan Santai',
            'tanggal' => today()->addDays(5),
            'jam' => '06:00',
            'lokasi' => 'Lapangan RT',
            'status' => 'disetujui',
        ]);

        $this->actingAs($warga)->get(route('acara.edit', $acara))->assertStatus(403);
    }

    public function test_admin_can_reject_event_with_reason(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $warga = User::factory()->create(['role' => 'warga', 'status_verifikasi' => 'disetujui']);
        $acara = Acara::create([
            'user_id' => $warga->id,
            'judul' => 'Bazar Malam',
            'tanggal' => today()->addDays(3),
            'jam' => '18:00',
            'lokasi' => 'Jalan Utama',
            'status' => 'menunggu',
        ]);

        $response = $this->actingAs($admin)->put(route('acara.update', $acara), [
            'judul' => $acara->judul,
            'tanggal' => $acara->tanggal->toDateString(),
            'jam' => $acara->jam,
            'lokasi' => $acara->lokasi,
            'status' => 'ditolak',
            'alasan_penolakan' => 'Jalan utama tidak boleh ditutup pada hari kerja.',
        ]);

        $response->assertRedirect(route('acara.index'));
        $this->assertSame('ditolak', $acara->fresh()->status);
        $this->assertSame('Jalan utama tidak boleh ditutup pada hari kerja.', $acara->fresh()->alasan_penolakan);
        $this->assertSame(1, $warga->notifications()->count());
    }

    public function test_validation_errors_use_indonesian_language(): void
    {
        $warga = User::factory()->create(['role' => 'warga', 'status_verifikasi' => 'disetujui']);

        $response = $this->actingAs($warga)->post(route('forum.store'), [
            'judul' => '',
            'isi' => '',
        ]);

        $response->assertSessionHasErrors(['judul', 'isi', 'label']);
        $errors = session('errors')->getBag('default');
        $this->assertStringContainsString('wajib diisi', $errors->first('judul'));
    }
}
