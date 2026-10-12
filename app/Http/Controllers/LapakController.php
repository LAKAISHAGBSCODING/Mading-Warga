<?php

namespace App\Http\Controllers;

use App\Models\Lapak;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LapakController extends Controller
{
    public function index()
    {
        return view('lapak.index', [
            'items' => Lapak::with('user')->latest()->paginate(12),
        ]);
    }

    public function create()
    {
        return view('lapak.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama' => 'required|string|max:150',
            'harga' => 'required|integer|min:0',
            'deskripsi' => 'required|string',
            'foto' => 'nullable|image|max:5120',
        ]);
        unset($data['foto']);

        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')->store('lapak', 'public');
        }

        Lapak::create($data + ['user_id' => $request->user()->id]);

        return redirect()->route('lapak.index')->with('sukses', 'Lapak kamu sudah tayang.');
    }

    public function destroy(Lapak $lapak)
    {
        $u = auth()->user();
        abort_unless($u->isAdmin() || $u->id === $lapak->user_id, 403);

        if ($lapak->foto) {
            Storage::disk('public')->delete($lapak->foto);
        }
        $lapak->delete();

        return redirect()->route('lapak.index')->with('sukses', 'Lapak dihapus.');
    }
}