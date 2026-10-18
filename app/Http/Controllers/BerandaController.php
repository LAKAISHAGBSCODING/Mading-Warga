<?php

namespace App\Http\Controllers;

use App\Models\Acara;
use App\Models\Pengumuman;
use App\Models\User;

class BerandaController extends Controller
{
    public function index()
    {
        return view('beranda', [
            'pengumumans' => Pengumuman::orderByDesc('is_pinned')
                ->orderByDesc('pinned_at')
                ->latest()
                ->take(3)
                ->get(),
            'acaras' => Acara::where('status', 'disetujui')
                ->whereDate('tanggal', '>=', today())
                ->orderBy('tanggal')->orderBy('jam')->take(3)->get(),
            'jumlahPending' => auth()->user()->isAdmin()
                ? User::where('role', 'warga')->where('status_verifikasi', 'pending')->count()
                : 0,
        ]);
    }
}
