<?php

namespace App\Http\Controllers;

use App\Notifications\AcaraDisetujuiNotification;
use App\Notifications\AdminUsulanAcaraNotification;
use App\Notifications\PengumumanBaruNotification;
use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;

class NotifikasiController extends Controller
{
    public function index(Request $request)
    {
        return view('notifikasi.index', [
            'notifications' => $request->user()->notifications()->latest()->paginate(15),
            'jumlahBelumDibaca' => $request->user()->unreadNotifications()->count(),
        ]);
    }

    public function baca(Request $request, DatabaseNotification $notification)
    {
        $notification = $request->user()
            ->notifications()
            ->whereKey($notification->getKey())
            ->firstOrFail();

        $notification->markAsRead();

        return redirect()->to($this->urlTujuan($notification));
    }

    public function bacaSemua(Request $request)
    {
        $request->user()->unreadNotifications()->update(['read_at' => now()]);

        return redirect()->route('notifikasi.index')->with('sukses', 'Semua notifikasi ditandai sudah dibaca.');
    }

    private function urlTujuan(DatabaseNotification $notification): string
    {
        return match ($notification->type) {
            PengumumanBaruNotification::class => route('pengumuman.index'),
            AcaraDisetujuiNotification::class => route('acara.index').'#acara-'.(int) $notification->data['acara_id'],
            AdminUsulanAcaraNotification::class => route('acara.edit', (int) $notification->data['acara_id']),
            default => route('notifikasi.index'),
        };
    }
}
