<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

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

        $user->fill([
            'name' => $data['name'],
            'email' => $data['email'],
            'no_rumah' => $data['no_rumah'],
            'no_wa' => $wa,
        ]);

        if ($request->boolean('hapus_foto') && $user->foto) {
            Storage::disk('public')->delete($user->foto);
            $user->foto = null;
        }

        if ($request->hasFile('foto')) {
            if ($user->foto) {
                Storage::disk('public')->delete($user->foto);
            }
            $user->foto = $request->file('foto')->store('profil', 'public');
        }

        $user->save();

        return redirect()->route('profile.edit')->with('sukses', 'Profil berhasil diperbarui.');
    }

    public function destroy(Request $request)
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();
        abort_if($user->isAdmin(), 403, 'Akun pengurus tidak bisa dihapus dari sini.');

        Auth::logout();

        if ($user->foto) {
            Storage::disk('public')->delete($user->foto);
        }
        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}