<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Throwable;

class ProfileController extends Controller
{
    public function edit(Request $request)
    {
        return view('profile.edit', ['user' => $request->user()]);
    }

    public function update(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique(User::class)->ignore($user->id)],
            'no_rumah' => ['required', 'string', 'max:50'],
            'no_wa' => ['required', 'regex:/^[0-9+\s-]{9,20}$/'],
            'foto' => ['nullable', 'image', 'max:3072'],
        ]);

        $wa = preg_replace('/\D/', '', $data['no_wa']);
        if (str_starts_with($wa, '0')) {
            $wa = '62'.substr($wa, 1);
        }

        $dataProfil = [
            'name' => $data['name'],
            'email' => $data['email'],
            'no_rumah' => $data['no_rumah'],
            'no_wa' => $wa,
        ];

        $fotoLama = $user->foto;
        $fotoBaru = null;

        if ($request->hasFile('foto')) {
            try {
                $fotoBaru = $request->file('foto')->store('profil', 'public');
                if ($fotoBaru === false) {
                    throw new \RuntimeException('Penyimpanan foto profil mengembalikan false.');
                }
            } catch (Throwable $e) {
                Log::error('Gagal mengunggah foto profil: '.$e->getMessage());

                return redirect()->back()->withInput()->with('error', 'Gagal memproses foto profil. Silakan coba kembali.');
            }

            $dataProfil['foto'] = $fotoBaru;
        } elseif ($request->boolean('hapus_foto') && $fotoLama) {
            if (! $this->hapusBerkas($fotoLama, 'foto profil')) {
                return redirect()->back()->withInput()->with('error', 'Foto profil lama tidak dapat dihapus. Perubahan profil belum disimpan.');
            }

            $dataProfil['foto'] = null;
        }

        if ($user->email !== $dataProfil['email']) {
            $user->email_verified_at = null;
        }

        $user->update($dataProfil);

        if ($fotoBaru !== null && $fotoLama && ! $this->hapusBerkas($fotoLama, 'foto profil lama')) {
            return redirect()->route('profile.edit')->with('error', 'Profil berhasil diperbarui, tetapi foto lama belum dapat dihapus.');
        }

        return redirect()->route('profile.edit')->with('sukses', 'Profil berhasil diperbarui.');
    }

    public function destroy(Request $request)
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();
        abort_if($user->isAdmin(), 403, 'Akun pengurus tidak bisa dihapus dari sini.');

        if ($user->foto) {
            if (! $this->hapusBerkas($user->foto, 'foto profil saat menghapus akun')) {
                return redirect()->back()->with('error', 'Foto profil tidak dapat dihapus. Akun belum dihapus.');
            }
        }

        Auth::logout();
        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
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
