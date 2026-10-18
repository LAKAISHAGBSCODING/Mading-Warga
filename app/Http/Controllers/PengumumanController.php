<?php

namespace App\Http\Controllers;

use App\Models\Pengumuman;
use App\Models\User;
use App\Notifications\PengumumanBaruNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;

class PengumumanController extends Controller
{
    private function aturan(): array
    {
        return [
            'judul' => 'required|string|max:200',
            'isi' => 'required|string',
            'lampiran' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ];
    }

    public function index()
    {
        return view('pengumuman.index', [
            'items' => Pengumuman::with('user')->latest()->paginate(10),
        ]);
    }

    public function create()
    {
        return view('pengumuman.form', ['item' => new Pengumuman]);
    }

    public function store(Request $request)
    {
        $data = $request->validate($this->aturan());
        unset($data['lampiran']);

        if ($request->hasFile('lampiran')) {
            try {
                $data['lampiran'] = $request->file('lampiran')->store('pengumuman', 'public');
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::error('Gagal mengunggah lampiran pengumuman: '.$e->getMessage());
                return redirect()->back()->withInput()->with('error', 'Gagal mengunggah berkas lampiran. Silakan coba kembali.');
            }
        }

        $pengumuman = Pengumuman::create($data + ['user_id' => $request->user()->id]);

        try {
            Notification::send(
                User::where('role', 'warga')->where('status_verifikasi', 'disetujui')->get(),
                new PengumumanBaruNotification($pengumuman)
            );
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Gagal mengirimkan notifikasi pengumuman baru: '.$e->getMessage());
        }

        return redirect()->route('pengumuman.index')->with('sukses', 'Pengumuman diterbitkan.');
    }

    public function edit(Pengumuman $pengumuman)
    {
        return view('pengumuman.form', ['item' => $pengumuman]);
    }

    public function update(Request $request, Pengumuman $pengumuman)
    {
        $data = $request->validate($this->aturan());
        unset($data['lampiran']);

        if ($request->hasFile('lampiran')) {
            try {
                if ($pengumuman->lampiran) {
                    Storage::disk('public')->delete($pengumuman->lampiran);
                }
                $data['lampiran'] = $request->file('lampiran')->store('pengumuman', 'public');
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::error('Gagal memperbarui lampiran pengumuman: '.$e->getMessage());
                return redirect()->back()->withInput()->with('error', 'Gagal memperbarui berkas lampiran. Silakan coba kembali.');
            }
        }

        $pengumuman->update($data);

        return redirect()->route('pengumuman.index')->with('sukses', 'Pengumuman diperbarui.');
    }

    public function destroy(Pengumuman $pengumuman)
    {
        if ($pengumuman->lampiran) {
            try {
                Storage::disk('public')->delete($pengumuman->lampiran);
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning('Gagal menghapus file lampiran pengumuman: '.$e->getMessage());
            }
        }
        $pengumuman->delete();

        return redirect()->route('pengumuman.index')->with('sukses', 'Pengumuman dihapus.');
    }
}
