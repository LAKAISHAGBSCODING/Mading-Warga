<?php

namespace App\Notifications;

use App\Models\Pengumuman;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class PengumumanBaruNotification extends Notification
{
    use Queueable;

    public function __construct(public Pengumuman $pengumuman) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'judul' => $this->pengumuman->judul,
            'pesan' => 'Pengurus RT menerbitkan pengumuman baru.',
        ];
    }
}
