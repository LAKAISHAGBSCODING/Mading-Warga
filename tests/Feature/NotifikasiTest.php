<?php

namespace Tests\Feature;

use App\Models\Acara;
use App\Models\Pengumuman;
use App\Models\User;
use App\Notifications\AcaraDisetujuiNotification;
use App\Notifications\AdminUsulanAcaraNotification;
use App\Notifications\PengumumanBaruNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotifikasiTest extends TestCase
{
    use RefreshDatabase;

    public function test_new_announcements_notify_approved_residents_only(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $warga = User::factory()->create(['role' => 'warga', 'status_verifikasi' => 'disetujui']);
        $pending = User::factory()->create(['role' => 'warga', 'status_verifikasi' => 'pending']);

        $response = $this->actingAs($admin)->post(route('pengumuman.store'), [
            'judul' => 'Kerja bakti',
            'isi' => 'Kerja bakti hari Minggu.',
        ]);

        $response->assertRedirect(route('pengumuman.index'));
        $this->assertDatabaseCount('notifications', 1);
        $this->assertSame(PengumumanBaruNotification::class, $warga->notifications()->first()->type);
        $this->assertCount(0, $pending->notifications);
    }

    public function test_approved_event_notifies_approved_residents(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $warga = User::factory()->create(['role' => 'warga', 'status_verifikasi' => 'disetujui']);
        $acara = Acara::create([
            'user_id' => $warga->id,
            'judul' => 'Rapat warga',
            'tanggal' => today()->addWeek(),
            'jam' => '19:00',
            'lokasi' => 'Balai warga',
            'status' => 'menunggu',
        ]);

        $response = $this->actingAs($admin)->patch(route('acara.update', $acara), [
            'judul' => $acara->judul,
            'tanggal' => $acara->tanggal->toDateString(),
            'jam' => $acara->jam,
            'lokasi' => $acara->lokasi,
            'status' => 'disetujui',
        ]);

        $response->assertRedirect(route('acara.index'));
        $this->assertDatabaseCount('notifications', 1);
        $this->assertSame(AcaraDisetujuiNotification::class, $warga->notifications()->first()->type);
    }

    public function test_events_published_by_admin_notify_approved_residents(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $warga = User::factory()->create(['role' => 'warga', 'status_verifikasi' => 'disetujui']);

        $response = $this->actingAs($admin)->post(route('acara.store'), [
            'judul' => 'Kerja bakti',
            'tanggal' => today()->addWeek()->toDateString(),
            'jam' => '08:00',
            'lokasi' => 'Taman warga',
        ]);

        $response->assertRedirect(route('acara.index'));
        $this->assertDatabaseCount('notifications', 1);
        $this->assertSame(AcaraDisetujuiNotification::class, $warga->notifications()->first()->type);
    }

    public function test_resident_event_proposals_notify_admins_only(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $secondAdmin = User::factory()->create(['role' => 'admin']);
        $warga = User::factory()->create(['role' => 'warga', 'status_verifikasi' => 'disetujui']);
        $anotherWarga = User::factory()->create(['role' => 'warga', 'status_verifikasi' => 'disetujui']);

        $response = $this->actingAs($warga)->post(route('acara.store'), [
            'judul' => 'Rapat warga',
            'tanggal' => today()->addWeek()->toDateString(),
            'jam' => '19:00',
            'lokasi' => 'Balai warga',
        ]);

        $acara = Acara::firstOrFail();
        $notification = $admin->notifications()->firstOrFail();

        $response->assertRedirect(route('acara.index'));
        $this->assertSame('menunggu', $acara->status);
        $this->assertSame(AdminUsulanAcaraNotification::class, $notification->type);
        $this->assertSame($acara->id, $notification->data['acara_id']);
        $this->assertSame(1, $admin->notifications()->count());
        $this->assertSame(1, $secondAdmin->notifications()->count());
        $this->assertSame(0, $warga->notifications()->count());
        $this->assertSame(0, $anotherWarga->notifications()->count());
    }

    public function test_admin_proposal_notification_opens_the_event_review_page(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $warga = User::factory()->create(['role' => 'warga', 'status_verifikasi' => 'disetujui']);
        $acara = Acara::create([
            'user_id' => $warga->id,
            'judul' => 'Rapat warga',
            'tanggal' => today()->addWeek(),
            'jam' => '19:00',
            'lokasi' => 'Balai warga',
            'status' => 'menunggu',
        ]);
        $admin->notify(new AdminUsulanAcaraNotification($acara, $warga->name));
        $notification = $admin->notifications()->firstOrFail();

        $response = $this->actingAs($admin)->patch(route('notifikasi.baca', $notification));

        $response->assertRedirect(route('acara.edit', $acara));
        $this->assertNotNull($notification->fresh()->read_at);
    }

    public function test_reading_a_notification_marks_it_read_and_opens_its_content(): void
    {
        $warga = User::factory()->create(['role' => 'warga', 'status_verifikasi' => 'disetujui']);
        $admin = User::factory()->create(['role' => 'admin']);
        $pengumuman = Pengumuman::create([
            'user_id' => $admin->id,
            'judul' => 'Jadwal ronda',
            'isi' => 'Cek jadwal ronda.',
        ]);
        $warga->notify(new PengumumanBaruNotification($pengumuman));
        $notification = $warga->notifications()->firstOrFail();

        $response = $this->actingAs($warga)->patch(route('notifikasi.baca', $notification));

        $response->assertRedirect(route('pengumuman.index'));
        $this->assertNotNull($notification->fresh()->read_at);
    }

    public function test_users_cannot_read_another_users_notification(): void
    {
        $owner = User::factory()->create(['role' => 'warga', 'status_verifikasi' => 'disetujui']);
        $other = User::factory()->create(['role' => 'warga', 'status_verifikasi' => 'disetujui']);
        $admin = User::factory()->create(['role' => 'admin']);
        $pengumuman = Pengumuman::create([
            'user_id' => $admin->id,
            'judul' => 'Jadwal ronda',
            'isi' => 'Cek jadwal ronda.',
        ]);
        $owner->notify(new PengumumanBaruNotification($pengumuman));
        $notification = $owner->notifications()->firstOrFail();

        $this->actingAs($other)
            ->patch(route('notifikasi.baca', $notification))
            ->assertNotFound();

        $this->assertNull($notification->fresh()->read_at);
    }

    public function test_residents_can_mark_all_notifications_as_read(): void
    {
        $warga = User::factory()->create(['role' => 'warga', 'status_verifikasi' => 'disetujui']);
        $admin = User::factory()->create(['role' => 'admin']);
        $pengumuman = Pengumuman::create([
            'user_id' => $admin->id,
            'judul' => 'Jadwal ronda',
            'isi' => 'Cek jadwal ronda.',
        ]);
        $warga->notify(new PengumumanBaruNotification($pengumuman));

        $this->actingAs($warga)
            ->patch(route('notifikasi.baca-semua'))
            ->assertRedirect(route('notifikasi.index'));

        $this->assertSame(0, $warga->fresh()->unreadNotifications()->count());
    }

    public function test_notification_page_lists_unread_notifications(): void
    {
        $this->withoutVite();
        $warga = User::factory()->create(['role' => 'warga', 'status_verifikasi' => 'disetujui']);
        $admin = User::factory()->create(['role' => 'admin']);
        $pengumuman = Pengumuman::create([
            'user_id' => $admin->id,
            'judul' => 'Jadwal ronda',
            'isi' => 'Cek jadwal ronda.',
        ]);
        $warga->notify(new PengumumanBaruNotification($pengumuman));

        $this->actingAs($warga)
            ->get(route('notifikasi.index'))
            ->assertOk()
            ->assertSee('Jadwal ronda')
            ->assertSee('Tandai semua sudah dibaca')
            ->assertSee('Notifikasi, 1 belum dibaca');
    }
}
