<?php

namespace App\Notifications;

use App\Models\Acara;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class AcaraDisetujuiNotification extends Notification
{
    use Queueable;

    public function __construct(public Acara $acara) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'acara_id' => $this->acara->id,
            'judul' => $this->acara->judul,
            'pesan' => 'Acara warga baru telah disetujui.',
        ];
    }
}
