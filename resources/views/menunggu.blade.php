<x-guest-layout>
    @if(auth()->user()->status_verifikasi === 'ditolak')
        <span class="inline-flex rounded-full bg-red-50 text-red-700 text-sm font-semibold px-3 py-1">Belum disetujui</span>
        <h1 class="mt-3 text-2xl font-extrabold text-slate-900">Pendaftaran belum disetujui</h1>
        <p class="mt-2 text-slate-600">Mohon hubungi pengurus RT untuk konfirmasi data kamu.</p>
    @else
        <span class="inline-flex rounded-full bg-blue-50 text-blue-700 text-sm font-semibold px-3 py-1">Menunggu verifikasi</span>
        <h1 class="mt-3 text-2xl font-extrabold text-slate-900">Akun sedang diperiksa</h1>
        <p class="mt-2 text-slate-600">Terima kasih sudah mendaftar. Pengurus RT sedang memeriksa data kamu. Silakan masuk lagi setelah akun disetujui.</p>
    @endif

    <form method="POST" action="{{ route('logout') }}" class="mt-6">
        @csrf
        <button class="btn-outline w-full">Keluar</button>
    </form>
</x-guest-layout>