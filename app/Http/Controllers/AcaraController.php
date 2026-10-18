<?php

namespace App\Http\Controllers;

use App\Models\Acara;
use App\Models\Rsvp;
use App\Models\User;
use App\Notifications\AcaraDisetujuiNotification;
use App\Notifications\AcaraDitolakNotification;
use App\Notifications\AdminUsulanAcaraNotification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Symfony\Component\HttpFoundation\StreamedResponse;

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
            'kuota' => 'nullable|integer|min:1',
        ];
    }

    public function index(Request $request)
    {
        $user = auth()->user();
        $q = trim((string) $request->query('q', ''));

        $acaras = Acara::with('rsvps.user')
            ->withCount([
                'rsvps as rsvp_hadir_count' => fn (Builder $query) => $query->where('status', 'hadir'),
                'rsvps as rsvp_tidak_hadir_count' => fn (Builder $query) => $query->where('status', 'tidak_hadir'),
            ])
            ->where('status', 'disetujui')
            ->whereDate('tanggal', '>=', today())
            ->when($q !== '', function (Builder $query) use ($q): void {
                $query->where(function (Builder $matches) use ($q): void {
                    $matches->where('judul', 'like', "%{$q}%")
                        ->orWhere('deskripsi', 'like', "%{$q}%")
                        ->orWhere('lokasi', 'like', "%{$q}%");
                });
            })
            ->orderBy('tanggal')->orderBy('jam')
            ->paginate(10)
            ->withQueryString();

        $usulanSaya = Acara::where('user_id', $user->id)
            ->where('status', '!=', 'disetujui')
            ->when($q !== '', function (Builder $query) use ($q): void {
                $query->where(function (Builder $matches) use ($q): void {
                    $matches->where('judul', 'like', "%{$q}%")
                        ->orWhere('deskripsi', 'like', "%{$q}%")
                        ->orWhere('lokasi', 'like', "%{$q}%");
                });
            })
            ->latest()->get();

        $menunggu = $user->isAdmin()
            ? Acara::with('pengaju')->where('status', 'menunggu')
                ->when($q !== '', function (Builder $query) use ($q): void {
                    $query->where(function (Builder $matches) use ($q): void {
                        $matches->where('judul', 'like', "%{$q}%")
                            ->orWhere('deskripsi', 'like', "%{$q}%")
                            ->orWhere('lokasi', 'like', "%{$q}%");
                    });
                })
                ->latest()->get()
            : collect();

        return view('acara.index', compact('acaras', 'usulanSaya', 'menunggu', 'q'));
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
            Log::warning('Gagal mengirimkan notifikasi acara: '.$e->getMessage());
        }

        return redirect()->route('acara.index')->with(
            'sukses',
            $admin ? 'Acara dipublikasikan.' : 'Usulan terkirim. Menunggu persetujuan pengurus RT.'
        );
    }

    public function edit(Acara $acara)
    {
        $u = auth()->user();
        if (! $u->isAdmin()) {
            abort_unless($u->id === $acara->user_id && $acara->status === 'menunggu', 403);
        }

        return view('acara.form', ['item' => $acara]);
    }

    public function update(Request $request, Acara $acara)
    {
        $u = $request->user();
        $admin = $u->isAdmin();

        if (! $admin) {
            abort_unless($u->id === $acara->user_id && $acara->status === 'menunggu', 403);
        }

        $rules = $this->aturan();
        if ($admin) {
            $rules['status'] = 'required|in:menunggu,disetujui,ditolak';
            $rules['alasan_penolakan'] = 'nullable|string|max:500';
        }

        $data = $request->validate($rules);

        $statusLama = $acara->status;
        $statusBaru = $admin ? ($data['status'] ?? $statusLama) : 'menunggu';
        $data['status'] = $statusBaru;

        if ($statusBaru !== 'ditolak') {
            $data['alasan_penolakan'] = null;
        }

        $acara->update($data);

        // Notifikasi jika baru disetujui
        if ($statusLama !== 'disetujui' && $statusBaru === 'disetujui') {
            try {
                Notification::send(
                    User::where('role', 'warga')->where('status_verifikasi', 'disetujui')->get(),
                    new AcaraDisetujuiNotification($acara)
                );
            } catch (\Throwable $e) {
                Log::warning('Gagal mengirim notifikasi persetujuan acara: '.$e->getMessage());
            }
        }

        // Notifikasi jika baru ditolak (ke pengusul)
        if ($statusLama !== 'ditolak' && $statusBaru === 'ditolak' && $acara->pengaju) {
            try {
                $acara->pengaju->notify(new AcaraDitolakNotification($acara, $data['alasan_penolakan'] ?? null));
            } catch (\Throwable $e) {
                Log::warning('Gagal mengirim notifikasi penolakan acara: '.$e->getMessage());
            }
        }

        return redirect()->route('acara.index')->with('sukses', 'Acara diperbarui.');
    }

    public function destroy(Acara $acara)
    {
        $u = auth()->user();
        if (! $u->isAdmin()) {
            abort_unless($u->id === $acara->user_id && $acara->status === 'menunggu', 403);
        }

        $acara->delete();

        return redirect()->route('acara.index')->with('sukses', 'Acara dihapus.');
    }

    public function rsvp(Request $request, Acara $acara)
    {
        $data = $request->validate(['status' => 'required|in:hadir,tidak_hadir']);

        $tersimpan = DB::transaction(function () use ($acara, $request, $data): bool {
            $acaraTerkunci = Acara::query()->lockForUpdate()->findOrFail($acara->id);
            abort_unless($acaraTerkunci->status === 'disetujui', 404);

            $rsvp = Rsvp::query()
                ->where('acara_id', $acaraTerkunci->id)
                ->where('user_id', $request->user()->id)
                ->lockForUpdate()
                ->first();

            if (
                $data['status'] === 'hadir'
                && $acaraTerkunci->kuota !== null
                && $rsvp?->status !== 'hadir'
                && $acaraTerkunci->rsvps()->where('status', 'hadir')->count() >= $acaraTerkunci->kuota
            ) {
                return false;
            }

            Rsvp::updateOrCreate(
                ['acara_id' => $acaraTerkunci->id, 'user_id' => $request->user()->id],
                ['status' => $data['status']]
            );

            return true;
        });

        if (! $tersimpan) {
            return redirect()->back()->with('error', 'Kuota peserta sudah penuh. RSVP hadir tidak dapat ditambahkan.');
        }

        return redirect()
            ->to(route('acara.index').'#acara-'.$acara->id)
            ->with('sukses', 'Jawaban kamu tersimpan.');
    }

    public function exportRsvp(Acara $acara): StreamedResponse
    {
        return response()->streamDownload(function () use ($acara): void {
            $output = fopen('php://output', 'wb');
            if ($output === false) {
                throw new \RuntimeException('Tidak dapat membuka aliran ekspor RSVP.');
            }

            fwrite($output, "\xEF\xBB\xBF");
            fputcsv($output, ['Nama', 'No. Rumah', 'No. WA', 'Status RSVP', 'Waktu Konfirmasi'], escape: '');

            foreach ($acara->rsvps()->with('user')->orderBy('created_at')->lazy() as $rsvp) {
                fputcsv($output, [
                    $this->nilaiCsv($rsvp->user->name),
                    $this->nilaiCsv($rsvp->user->no_rumah ?? ''),
                    $this->nilaiCsv($rsvp->user->no_wa ?? ''),
                    $rsvp->status,
                    $rsvp->created_at->format('Y-m-d H:i:s'),
                ], escape: '');
            }

            fclose($output);
        }, 'rsvp-acara-'.$acara->id.'.csv', [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    private function nilaiCsv(string $value): string
    {
        if (preg_match('/^[\t\r ]*[=+\-@]/u', $value) === 1) {
            return "'".$value;
        }

        return $value;
    }
}
