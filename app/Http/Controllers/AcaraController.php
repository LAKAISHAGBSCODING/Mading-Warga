<?php

namespace App\Http\Controllers;

use App\Models\Acara;
use App\Models\Rsvp;
use Illuminate\Http\Request;

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

        Acara::create($data + [
            'user_id' => $request->user()->id,
            'status' => $admin ? 'disetujui' : 'menunggu',
        ]);

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

        $acara->update($data);

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