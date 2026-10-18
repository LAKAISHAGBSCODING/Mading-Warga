<?php

namespace App\Http\Controllers;

use App\Models\Lapak;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Throwable;

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
                $foto = $request->file('foto')->store('lapak', 'public');
                if ($foto === false) {
                    throw new \RuntimeException('Penyimpanan foto lapak mengembalikan false.');
                }
                $data['foto'] = $foto;
            } catch (Throwable $e) {
                Log::error('Gagal mengunggah foto lapak: '.$e->getMessage());

                return redirect()->back()->withInput()->with('error', 'Gagal mengunggah foto lapak. Silakan coba lagi.');
            }
        }

        Lapak::create($data + ['user_id' => $request->user()->id]);

        return redirect()->route('lapak.index')->with('sukses', 'Lapak kamu sudah tayang.');
    }

    public function edit(Lapak $lapak)
    {
        $u = auth()->user();
        abort_unless($u->isAdmin() || $u->id === $lapak->user_id, 403);

        return view('lapak.create', ['lapak' => $lapak]);
    }

    public function update(Request $request, Lapak $lapak)
    {
        $u = auth()->user();
        abort_unless($u->isAdmin() || $u->id === $lapak->user_id, 403);

        $data = $request->validate([
            'nama' => 'required|string|max:150',
            'harga' => 'required|integer|min:0',
            'deskripsi' => 'required|string',
            'foto' => 'nullable|image|max:5120',
        ]);
        unset($data['foto']);

        $fotoLama = $lapak->foto;
        $fotoBaru = null;
        if ($request->hasFile('foto')) {
            try {
                $fotoBaru = $request->file('foto')->store('lapak', 'public');
                if ($fotoBaru === false) {
                    throw new \RuntimeException('Penyimpanan foto lapak mengembalikan false.');
                }
                $data['foto'] = $fotoBaru;
            } catch (Throwable $e) {
                Log::error('Gagal memperbarui foto lapak: '.$e->getMessage());

                return redirect()->back()->withInput()->with('error', 'Gagal mengunggah foto baru. Silakan coba lagi.');
            }
        } elseif ($request->boolean('hapus_foto') && $fotoLama) {
            if (! $this->hapusBerkas($fotoLama, 'foto lapak')) {
                return redirect()->back()->withInput()->with('error', 'Foto lama tidak dapat dihapus. Lapak belum diperbarui.');
            }
            $data['foto'] = null;
        }

        $lapak->update($data);

        if ($fotoBaru !== null && $fotoLama && ! $this->hapusBerkas($fotoLama, 'foto lapak lama')) {
            return redirect()->route('lapak.index')->with('error', 'Lapak berhasil diperbarui, tetapi foto lama belum dapat dihapus.');
        }

        return redirect()->route('lapak.index')->with('sukses', 'Lapak berhasil diperbarui.');
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
            if (! $this->hapusBerkas($lapak->foto, 'foto lapak')) {
                return redirect()->back()->with('error', 'Foto lapak tidak dapat dihapus. Lapak belum dihapus.');
            }
        }
        $lapak->delete();

        return redirect()->route('lapak.index')->with('sukses', 'Lapak dihapus.');
    }

    private function hapusBerkas(string $path, string $konteks): bool
    {
        try {
            $dihapus = Storage::disk('public')->delete($path);
        } catch (Throwable $e) {
            Log::warning("Gagal menghapus {$konteks}: ".$e->getMessage());

            return false;
        }

        if (! $dihapus) {
            Log::warning("Gagal menghapus {$konteks}; disk mengembalikan false.", ['path' => $path]);
        }

        return $dihapus;
    }
}
