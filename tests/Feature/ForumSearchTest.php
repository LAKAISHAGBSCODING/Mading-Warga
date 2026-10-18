<?php

namespace Tests\Feature;

use App\Models\ForumPost;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ForumSearchTest extends TestCase
{
    use RefreshDatabase;

    public function test_forum_search_filters_posts_and_keeps_query_in_pagination_links(): void
    {
        $resident = User::factory()->create(['role' => 'warga', 'status_verifikasi' => 'disetujui']);
        foreach (range(1, 11) as $number) {
            ForumPost::create([
                'user_id' => $resident->id,
                'label' => 'Info',
                'judul' => 'Balai warga nomor '.$number,
                'isi' => 'Pertemuan rutin lingkungan.',
            ]);
        }
        ForumPost::create([
            'user_id' => $resident->id,
            'label' => 'Info',
            'judul' => 'Jadwal posyandu',
            'isi' => 'Informasi kesehatan.',
        ]);

        $this->actingAs($resident)
            ->get(route('forum.index', ['q' => 'Balai']))
            ->assertOk()
            ->assertSee('Balai warga nomor')
            ->assertDontSee('Jadwal posyandu')
            ->assertSee('page=2')
            ->assertSee('q=Balai');
    }
}
