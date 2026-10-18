<?php

namespace App\Http\Controllers;

use App\Models\Lapak;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LapakController extends Controller
{
   public function index(Request $request)
{
    $q = trim((string) $request->query('q', ''));

    $items = Lapak::with('user')
        ->when($q !== '', function ($query) use ($q) {
            $query->where(function ($w) use ($q) {
                $w->where('nama', 'like', "%{$q}%")
                  ->orWhere('deskripsi', 'like', "%{$q}%")
                  ->orWhereHas('user', fn ($u) => $u->where('name', 'like', "%{$q}%"));
            });
        })
        ->orderByDesc('tersedia')
        ->latest()
        ->paginate(12)
        ->withQueryString();

    return view('lapak.index', compact('items', 'q'));
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
            try {
                $data['foto'] = $request->file('foto')->store('lapak', 'public');
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::error('Gagal mengunggah foto lapak: '.$e->getMessage());
                return redirect()->back()->withInput()->with('error', 'Gagal mengunggah foto lapak. Silakan coba lagi.');
            }
        }

        Lapak::create($data + ['user_id' => $request->user()->id]);

        return redirect()->route('lapak.index')->with('sukses', 'Lapak kamu sudah tayang.');
    }

    public function stok(Lapak $lapak)
    {
        $u = auth()->user();
        abort_unless($u->isAdmin() || $u->id === $lapak->user_id, 403);

        $lapak->update(['tersedia' => ! $lapak->tersedia]);

        return redirect()
            ->to(route('lapak.index', request()->only('q', 'page')).'#lapak-'.$lapak->id)
            ->with('sukses', $lapak->tersedia ? 'Ditandai tersedia.' : 'Ditandai habis.');
    }

    public function destroy(Lapak $lapak)
    {
        $u = auth()->user();
        abort_unless($u->isAdmin() || $u->id === $lapak->user_id, 403);

        if ($lapak->foto) {
            try {
                Storage::disk('public')->delete($lapak->foto);
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning('Gagal menghapus foto lapak: '.$e->getMessage());
            }
        }
        $lapak->delete();

        return redirect()->route('lapak.index')->with('sukses', 'Lapak dihapus.');
    }
}