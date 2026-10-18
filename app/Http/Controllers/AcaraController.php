<?php

namespace App\Http\Controllers;

use App\Models\Acara;
use App\Models\Rsvp;
use App\Models\User;
use App\Notifications\AcaraDisetujuiNotification;
use App\Notifications\AdminUsulanAcaraNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;

class AcaraController extends Controller
{
    private function aturan(): array
    {
        return [
            'judul' => 'required|string|max:200',
            'deskripsi' => 'nullable|string',
            'tanggal' => 'required|date',
            'jam' => 'required',
            'lokasi' => 'required|string|max:200',
        ];
    }

    public function index()
    {
        $user = auth()->user();

        $acaras = Acara::with('rsvps.user')
            ->where('status', 'disetujui')
            ->whereDate('tanggal', '>=', today())
            ->orderBy('tanggal')->orderBy('jam')->get();

        $usulanSaya = Acara::where('user_id', $user->id)
            ->where('status', '!=', 'disetujui')->latest()->get();

        $menunggu = $user->isAdmin()
            ? Acara::with('pengaju')->where('status', 'menunggu')->latest()->get()
            : collect();

        return view('acara.index', compact('acaras', 'usulanSaya', 'menunggu'));
    }

    public function create()
    {
        return view('acara.form', ['item' => new Acara]);
    }

    public function store(Request $request)
    {
        $data = $request->validate($this->aturan());
        $admin = $request->user()->isAdmin();

        $acara = Acara::create($data + [
            'user_id' => $request->user()->id,
            'status' => $admin ? 'disetujui' : 'menunggu',
        ]);

        try {
            if ($admin) {
                Notification::send(
                    User::where('role', 'warga')->where('status_verifikasi', 'disetujui')->get(),
                    new AcaraDisetujuiNotification($acara)
                );
            } else {
                Notification::send(
                    User::where('role', 'admin')->get(),
                    new AdminUsulanAcaraNotification($acara, $request->user()->name)
                );
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Gagal mengirimkan notifikasi acara: '.$e->getMessage());
        }

        return redirect()->route('acara.index')->with(
            'sukses',
            $admin ? 'Acara dipublikasikan.' : 'Usulan terkirim. Menunggu persetujuan pengurus RT.'
        );
    }

    public function edit(Acara $acara)
    {
        return view('acara.form', ['item' => $acara]);
    }

    public function update(Request $request, Acara $acara)
    {
        $data = $request->validate($this->aturan() + [
            'status' => 'required|in:menunggu,disetujui,ditolak',
        ]);

        $baruDisetujui = $acara->status !== 'disetujui' && $data['status'] === 'disetujui';

        $acara->update($data);

        if ($baruDisetujui) {
            try {
                Notification::send(
                    User::where('role', 'warga')->where('status_verifikasi', 'disetujui')->get(),
                    new AcaraDisetujuiNotification($acara)
                );
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning('Gagal mengirim notifikasi persetujuan acara: '.$e->getMessage());
            }
        }

        return redirect()->route('acara.index')->with('sukses', 'Acara diperbarui.');
    }

    public function destroy(Acara $acara)
    {
        $acara->delete();

        return redirect()->route('acara.index')->with('sukses', 'Acara dihapus.');
    }

    public function rsvp(Request $request, Acara $acara)
    {
        abort_unless($acara->status === 'disetujui', 404);

        $data = $request->validate(['status' => 'required|in:hadir,tidak_hadir']);

        Rsvp::updateOrCreate(
            ['acara_id' => $acara->id, 'user_id' => $request->user()->id],
            ['status' => $data['status']]
        );

        return redirect()
            ->to(route('acara.index').'#acara-'.$acara->id)
            ->with('sukses', 'Jawaban kamu tersimpan.');
    }
}
