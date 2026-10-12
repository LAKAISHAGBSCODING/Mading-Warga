<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class WargaController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status', 'pending');

        $warga = User::where('role', 'warga')
            ->where('status_verifikasi', $status)
            ->latest()->get();

        return view('warga.index', compact('warga', 'status'));
    }

    public function update(Request $request, User $user)
    {
        abort_if($user->isAdmin(), 403);

        $data = $request->validate(['status' => 'required|in:pending,disetujui,ditolak']);
        $user->update(['status_verifikasi' => $data['status']]);

        return back()->with('sukses', 'Status akun '.$user->name.' diperbarui.');
    }
}