<?php

namespace App\Notifications;

use App\Models\Acara;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class AdminUsulanAcaraNotification extends Notification
{
    use Queueable;

    public function __construct(
        public Acara $acara,
        public string $namaPengaju,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'acara_id' => $this->acara->id,
            'judul' => 'Usulan acara baru: '.$this->acara->judul,
            'pesan' => $this->namaPengaju.' mengusulkan acara. Tinjau usulan ini.',
        ];
    }
}
