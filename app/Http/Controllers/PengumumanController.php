<?php

namespace App\Http\Controllers;

use App\Models\Pengumuman;
use App\Models\PengumumanDibaca;
use App\Models\User;
use App\Notifications\PengumumanBaruNotification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Throwable;

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

    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        $user = auth()->user();

        $items = Pengumuman::with('user')
            ->withCount([
                'dibacas as dibaca_warga_count' => function (Builder $query) {
                    $query->whereHas('user', function (Builder $u) {
                        $u->where('role', 'warga')->where('status_verifikasi', 'disetujui');
                    });
                },
                'dibacas as sudah_dibaca_count' => function (Builder $query) use ($user) {
                    $query->where('user_id', $user->id);
                },
            ])
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($w) use ($q) {
                    $w->where('judul', 'like', "%{$q}%")
                        ->orWhere('isi', 'like', "%{$q}%");
                });
            })
            ->orderByDesc('is_pinned')
            ->orderByDesc('pinned_at')
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $totalWarga = User::where('role', 'warga')->where('status_verifikasi', 'disetujui')->count();

        return view('pengumuman.index', compact('items', 'q', 'totalWarga'));
    }

    public function show(Pengumuman $pengumuman)
    {
        $user = auth()->user();

        if (! $user->isAdmin()) {
            PengumumanDibaca::firstOrCreate([
                'pengumuman_id' => $pengumuman->id,
                'user_id' => $user->id,
            ]);
        }

        $totalWarga = User::where('role', 'warga')->where('status_verifikasi', 'disetujui')->count();
        $pembacaList = $user->isAdmin()
            ? $pengumuman->pembacas()->where('role', 'warga')->where('status_verifikasi', 'disetujui')->get()
            : collect();

        $belumMembaca = $user->isAdmin()
            ? User::where('role', 'warga')
                ->where('status_verifikasi', 'disetujui')
                ->whereNotIn('id', $pembacaList->pluck('id'))
                ->get()
            : collect();

        return view('pengumuman.show', compact('pengumuman', 'totalWarga', 'pembacaList', 'belumMembaca'));
    }

    public function togglePin(Pengumuman $pengumuman)
    {
        abort_unless(auth()->user()->isAdmin(), 403);

        $pesan = DB::transaction(function () use ($pengumuman): ?string {
            Pengumuman::query()->orderBy('id')->lockForUpdate()->get(['id']);
            $pengumuman->refresh();

            if (! $pengumuman->is_pinned) {
                $pinnedCount = Pengumuman::where('is_pinned', true)->count();
                if ($pinnedCount >= 3) {
                    return null;
                }

                $pengumuman->update([
                    'is_pinned' => true,
                    'pinned_at' => now(),
                ]);

                return 'Pengumuman berhasil disematkan.';
            }

            $pengumuman->update([
                'is_pinned' => false,
                'pinned_at' => null,
            ]);

            return 'Sematan pengumuman berhasil dilepas.';
        });

        if ($pesan === null) {
            return redirect()->back()->with('error', 'Maksimal 3 pengumuman yang dapat disematkan.');
        }

        return redirect()->back()->with('sukses', $pesan);
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
                $lampiran = $request->file('lampiran')->store('pengumuman', 'public');
                if ($lampiran === false) {
                    throw new \RuntimeException('Penyimpanan lampiran pengumuman mengembalikan false.');
                }
                $data['lampiran'] = $lampiran;
            } catch (Throwable $e) {
                Log::error('Gagal mengunggah lampiran pengumuman: '.$e->getMessage());

                return redirect()->back()->withInput()->with('error', 'Gagal mengunggah berkas lampiran. Silakan coba kembali.');
            }
        }

        $pengumuman = Pengumuman::create($data + ['user_id' => $request->user()->id]);

        try {
            Notification::send(
                User::where('role', 'warga')->where('status_verifikasi', 'disetujui')->get(),
                new PengumumanBaruNotification($pengumuman)
            );
        } catch (Throwable $e) {
            Log::warning('Gagal mengirimkan notifikasi pengumuman baru: '.$e->getMessage());
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

        $lampiranLama = $pengumuman->lampiran;
        $lampiranBaru = null;
        if ($request->hasFile('lampiran')) {
            try {
                $lampiranBaru = $request->file('lampiran')->store('pengumuman', 'public');
                if ($lampiranBaru === false) {
                    throw new \RuntimeException('Penyimpanan lampiran pengumuman mengembalikan false.');
                }
                $data['lampiran'] = $lampiranBaru;
            } catch (Throwable $e) {
                Log::error('Gagal memperbarui lampiran pengumuman: '.$e->getMessage());

                return redirect()->back()->withInput()->with('error', 'Gagal memperbarui berkas lampiran. Silakan coba kembali.');
            }
        }

        $pengumuman->update($data);

        if ($lampiranBaru !== null && $lampiranLama && ! $this->hapusBerkas($lampiranLama, 'lampiran pengumuman lama')) {
            return redirect()->route('pengumuman.index')->with('error', 'Pengumuman berhasil diperbarui, tetapi lampiran lama belum dapat dihapus.');
        }

        return redirect()->route('pengumuman.index')->with('sukses', 'Pengumuman diperbarui.');
    }

    public function destroy(Pengumuman $pengumuman)
    {
        if ($pengumuman->lampiran) {
            if (! $this->hapusBerkas($pengumuman->lampiran, 'lampiran pengumuman')) {
                return redirect()->back()->with('error', 'Lampiran pengumuman tidak dapat dihapus. Pengumuman belum dihapus.');
            }
        }
        $pengumuman->delete();

        return redirect()->route('pengumuman.index')->with('sukses', 'Pengumuman dihapus.');
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
