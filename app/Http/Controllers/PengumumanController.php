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
            $data['lampiran'] = $request->file('lampiran')->store('pengumuman', 'public');
        }

        $pengumuman = Pengumuman::create($data + ['user_id' => $request->user()->id]);

        Notification::send(
            User::where('role', 'warga')->where('status_verifikasi', 'disetujui')->get(),
            new PengumumanBaruNotification($pengumuman)
        );

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
            if ($pengumuman->lampiran) {
                Storage::disk('public')->delete($pengumuman->lampiran);
            }
            $data['lampiran'] = $request->file('lampiran')->store('pengumuman', 'public');
        }

        $pengumuman->update($data);

        return redirect()->route('pengumuman.index')->with('sukses', 'Pengumuman diperbarui.');
    }

    public function destroy(Pengumuman $pengumuman)
    {
        if ($pengumuman->lampiran) {
            Storage::disk('public')->delete($pengumuman->lampiran);
        }
        $pengumuman->delete();

        return redirect()->route('pengumuman.index')->with('sukses', 'Pengumuman dihapus.');
    }
}
