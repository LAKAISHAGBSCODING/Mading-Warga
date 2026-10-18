<?php

namespace Tests\Feature;

use App\Models\Pengumuman;
use App\Models\PengumumanDibaca;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PengumumanPinDibacaTest extends TestCase
{
    use RefreshDatabase;

    public function test_only_admin_can_pin_an_announcement(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $resident = User::factory()->create(['role' => 'warga', 'status_verifikasi' => 'disetujui']);
        $pengumuman = $this->buatPengumuman($admin);

        $this->actingAs($resident)
            ->patch(route('pengumuman.pin', $pengumuman))
            ->assertForbidden();

        $this->actingAs($admin)
            ->patch(route('pengumuman.pin', $pengumuman))
            ->assertRedirect();

        $this->assertTrue($pengumuman->fresh()->is_pinned);
    }

    public function test_existing_attachment_is_preserved_when_replacement_upload_fails(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $pengumuman = $this->buatPengumuman($admin, ['lampiran' => 'pengumuman/lama.pdf']);
        $disk = \Mockery::mock();
        $disk->shouldReceive('putFileAs')->once()->andReturn(false);
        Storage::shouldReceive('disk')->with('public')->once()->andReturn($disk);

        $response = $this->actingAs($admin)->from(route('pengumuman.index'))
            ->put(route('pengumuman.update', $pengumuman), [
                'judul' => 'Informasi diperbarui',
                'isi' => 'Isi informasi terbaru.',
                'lampiran' => UploadedFile::fake()->create('baru.pdf', 10, 'application/pdf'),
            ]);

        $response->assertRedirect(route('pengumuman.index'))
            ->assertSessionHas('error', 'Gagal memperbarui berkas lampiran. Silakan coba kembali.');
        $this->assertSame('pengumuman/lama.pdf', $pengumuman->fresh()->lampiran);
    }

    public function test_announcement_is_not_deleted_when_attachment_deletion_fails(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $pengumuman = $this->buatPengumuman($admin, ['lampiran' => 'pengumuman/lama.pdf']);
        $disk = \Mockery::mock();
        $disk->shouldReceive('delete')->once()->with('pengumuman/lama.pdf')->andReturn(false);
        Storage::shouldReceive('disk')->with('public')->once()->andReturn($disk);

        $response = $this->actingAs($admin)->from(route('pengumuman.index'))
            ->delete(route('pengumuman.destroy', $pengumuman));

        $response->assertRedirect(route('pengumuman.index'))
            ->assertSessionHas('error', 'Lampiran pengumuman tidak dapat dihapus. Pengumuman belum dihapus.');
        $this->assertModelExists($pengumuman);
    }

    public function test_admin_cannot_pin_more_than_three_announcements(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $announcements = collect(range(1, 4))
            ->map(fn (int $number): Pengumuman => $this->buatPengumuman($admin, [
                'judul' => 'Pengumuman '.$number,
                'is_pinned' => $number <= 3,
            ]));

        $this->actingAs($admin)
            ->from(route('pengumuman.index'))
            ->patch(route('pengumuman.pin', $announcements[3]))
            ->assertRedirect(route('pengumuman.index'))
            ->assertSessionHas('error', 'Maksimal 3 pengumuman yang dapat disematkan.');

        $this->assertFalse($announcements[3]->fresh()->is_pinned);
        $this->assertSame(3, Pengumuman::where('is_pinned', true)->count());
    }

    public function test_reopening_an_announcement_records_only_one_read_for_the_resident(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $resident = User::factory()->create(['role' => 'warga', 'status_verifikasi' => 'disetujui']);
        $pengumuman = $this->buatPengumuman($admin);

        $this->actingAs($resident)->get(route('pengumuman.show', $pengumuman))->assertOk();
        $this->get(route('pengumuman.show', $pengumuman))->assertOk();

        $this->assertSame(1, PengumumanDibaca::where('pengumuman_id', $pengumuman->id)
            ->where('user_id', $resident->id)
            ->count());
    }

    public function test_unread_resident_sees_new_badge_until_the_announcement_is_opened(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $resident = User::factory()->create(['role' => 'warga', 'status_verifikasi' => 'disetujui']);
        $pengumuman = $this->buatPengumuman($admin);

        $this->actingAs($resident)
            ->get(route('pengumuman.index'))
            ->assertOk()
            ->assertSee('Baru');

        $this->get(route('pengumuman.show', $pengumuman))->assertOk();

        $this->get(route('pengumuman.index'))
            ->assertOk()
            ->assertDontSee('Baru');
    }

    public function test_admin_sees_read_counts_and_only_approved_residents_in_the_reader_list(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $resident = User::factory()->create(['role' => 'warga', 'status_verifikasi' => 'disetujui', 'name' => 'Warga Disetujui']);
        $pending = User::factory()->create(['role' => 'warga', 'status_verifikasi' => 'pending', 'name' => 'Warga Pending']);
        $pengumuman = $this->buatPengumuman($admin);

        PengumumanDibaca::create([
            'pengumuman_id' => $pengumuman->id,
            'user_id' => $resident->id,
        ]);
        PengumumanDibaca::create([
            'pengumuman_id' => $pengumuman->id,
            'user_id' => $pending->id,
        ]);

        $this->actingAs($admin)
            ->get(route('pengumuman.show', $pengumuman))
            ->assertOk()
            ->assertSee('Dibaca 1 dari 1 warga yang disetujui.')
            ->assertSee('Warga Disetujui')
            ->assertDontSee('Warga Pending');
    }

    public function test_announcement_search_filters_results_and_preserves_query_in_pagination(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->buatPengumuman($admin, ['judul' => 'Jadwal kerja bakti']);
        $this->buatPengumuman($admin, ['judul' => 'Iuran bulanan']);

        $this->actingAs($admin)
            ->get(route('pengumuman.index', ['q' => 'kerja']))
            ->assertOk()
            ->assertSee('Jadwal kerja bakti')
            ->assertDontSee('Iuran bulanan');
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    private function buatPengumuman(User $admin, array $attributes = []): Pengumuman
    {
        return Pengumuman::create($attributes + [
            'user_id' => $admin->id,
            'judul' => 'Informasi warga',
            'isi' => 'Informasi untuk seluruh warga RT.',
        ]);
    }
}
