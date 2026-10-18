<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_profile_page_is_displayed(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->get('/profile');

        $response->assertOk();
    }

    public function test_profile_information_can_be_updated(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->patch('/profile', [
                'name' => 'Test User',
                'email' => 'test@example.com',
                'no_rumah' => 'A-12',
                'no_wa' => '081234567890',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/profile');

        $user->refresh();

        $this->assertSame('Test User', $user->name);
        $this->assertSame('test@example.com', $user->email);
        $this->assertNull($user->email_verified_at);
    }

    public function test_profile_photo_and_existing_value_are_preserved_when_upload_fails(): void
    {
        $user = User::factory()->create(['foto' => 'profil/foto-lama.png']);
        $namaLama = $user->name;
        $disk = \Mockery::mock();
        $disk->shouldReceive('putFileAs')->once()->andReturn(false);
        Storage::shouldReceive('disk')->with('public')->once()->andReturn($disk);

        $response = $this->actingAs($user)->from('/profile')->patch('/profile', [
            'name' => 'Nama Baru',
            'email' => $user->email,
            'no_rumah' => 'A-12',
            'no_wa' => '081234567890',
            'foto' => UploadedFile::fake()->image('foto-baru.png'),
        ]);

        $response->assertRedirect('/profile')
            ->assertSessionHas('error', 'Gagal memproses foto profil. Silakan coba kembali.');
        $this->assertSame($namaLama, $user->fresh()->name);
        $this->assertSame('profil/foto-lama.png', $user->fresh()->foto);
    }

    public function test_account_remains_when_profile_photo_deletion_fails(): void
    {
        $user = User::factory()->create(['foto' => 'profil/foto-lama.png']);
        $disk = \Mockery::mock();
        $disk->shouldReceive('delete')->once()->with('profil/foto-lama.png')->andReturn(false);
        Storage::shouldReceive('disk')->with('public')->once()->andReturn($disk);

        $response = $this->actingAs($user)->from('/profile')->delete('/profile', [
            'password' => 'password',
        ]);

        $response->assertRedirect('/profile')
            ->assertSessionHas('error', 'Foto profil tidak dapat dihapus. Akun belum dihapus.');
        $this->assertAuthenticatedAs($user);
        $this->assertModelExists($user);
    }

    public function test_email_verification_status_is_unchanged_when_the_email_address_is_unchanged(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->patch('/profile', [
                'name' => 'Test User',
                'email' => $user->email,
                'no_rumah' => $user->no_rumah ?? 'B-01',
                'no_wa' => $user->no_wa ?? '081298765432',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/profile');

        $this->assertNotNull($user->refresh()->email_verified_at);
    }

    public function test_user_can_delete_their_account(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->delete('/profile', [
                'password' => 'password',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/');

        $this->assertGuest();
        $this->assertNull($user->fresh());
    }

    public function test_correct_password_must_be_provided_to_delete_account(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->from('/profile')
            ->delete('/profile', [
                'password' => 'wrong-password',
            ]);

        $response
            ->assertSessionHasErrorsIn('userDeletion', 'password')
            ->assertRedirect('/profile');

        $this->assertNotNull($user->fresh());
    }
}
