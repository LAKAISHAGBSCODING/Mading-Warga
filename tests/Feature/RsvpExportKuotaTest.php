<?php

namespace Tests\Feature;

use App\Models\Acara;
use App\Models\Rsvp;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RsvpExportKuotaTest extends TestCase
{
    use RefreshDatabase;

    public function test_only_admin_can_download_an_event_rsvp_csv(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $resident = User::factory()->create(['role' => 'warga', 'status_verifikasi' => 'disetujui']);
        $acara = $this->buatAcara($admin);

        $this->actingAs($resident)
            ->get(route('acara.rsvp.export', $acara))
            ->assertForbidden();
    }

    public function test_admin_download_includes_csv_bom_headers_and_rsvp_rows(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $resident = User::factory()->create([
            'name' => 'Budi Santoso',
            'no_rumah' => 'A-12',
            'no_wa' => '+628123456789',
            'role' => 'warga',
            'status_verifikasi' => 'disetujui',
        ]);
        $acara = $this->buatAcara($admin);
        $rsvp = Rsvp::create([
            'acara_id' => $acara->id,
            'user_id' => $resident->id,
            'status' => 'hadir',
        ]);

        $response = $this->actingAs($admin)->get(route('acara.rsvp.export', $acara));

        $response->assertOk()->assertDownload('rsvp-acara-'.$acara->id.'.csv');
        $csv = $response->streamedContent();
        $this->assertStringStartsWith("\xEF\xBB\xBF", $csv);

        $rows = array_map(
            fn (string $line): array => str_getcsv($line, ',', '"', ''),
            explode("\n", trim(substr($csv, 3)))
        );
        $this->assertSame(['Nama', 'No. Rumah', 'No. WA', 'Status RSVP', 'Waktu Konfirmasi'], $rows[0]);
        $this->assertSame(['Budi Santoso', 'A-12', "'+628123456789", 'hadir'], array_slice($rows[1], 0, 4));
        $this->assertSame($rsvp->created_at->format('Y-m-d H:i:s'), $rows[1][4]);
    }

    public function test_new_hadir_rsvp_is_rejected_when_the_event_quota_is_full(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $firstResident = User::factory()->create(['role' => 'warga', 'status_verifikasi' => 'disetujui']);
        $nextResident = User::factory()->create(['role' => 'warga', 'status_verifikasi' => 'disetujui']);
        $acara = $this->buatAcara($admin, ['kuota' => 1]);
        Rsvp::create([
            'acara_id' => $acara->id,
            'user_id' => $firstResident->id,
            'status' => 'hadir',
        ]);

        $this->actingAs($nextResident)
            ->from(route('acara.index'))
            ->post(route('acara.rsvp', $acara), ['status' => 'hadir'])
            ->assertRedirect(route('acara.index'))
            ->assertSessionHas('error', 'Kuota peserta sudah penuh. RSVP hadir tidak dapat ditambahkan.');

        $this->assertDatabaseMissing('rsvps', [
            'acara_id' => $acara->id,
            'user_id' => $nextResident->id,
        ]);

        $this->post(route('acara.rsvp', $acara), ['status' => 'tidak_hadir'])
            ->assertRedirect(route('acara.index').'#acara-'.$acara->id);

        $this->assertDatabaseHas('rsvps', [
            'acara_id' => $acara->id,
            'user_id' => $nextResident->id,
            'status' => 'tidak_hadir',
        ]);
    }

    public function test_event_search_keeps_its_query_in_pagination_links(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        foreach (range(1, 11) as $number) {
            $this->buatAcara($admin, ['judul' => 'Rapat Balai '.$number]);
        }

        $this->actingAs($admin)
            ->get(route('acara.index', ['q' => 'Balai']))
            ->assertOk()
            ->assertSee('Rapat Balai 1')
            ->assertSee('page=2')
            ->assertSee('q=Balai');
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    private function buatAcara(User $admin, array $attributes = []): Acara
    {
        return Acara::create($attributes + [
            'user_id' => $admin->id,
            'judul' => 'Rapat warga',
            'deskripsi' => 'Pembahasan kegiatan lingkungan.',
            'tanggal' => today()->addDays(5),
            'jam' => '19:00',
            'lokasi' => 'Balai RT',
            'status' => 'disetujui',
        ]);
    }
}
