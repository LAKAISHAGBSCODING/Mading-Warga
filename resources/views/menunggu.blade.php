<x-guest-layout>
    @if(auth()->user()->status_verifikasi === 'ditolak')
        <h1 class="text-xl font-bold mb-2 text-red-600">Pendaftaran Belum Disetujui</h1>
        <p class="text-gray-600">Mohon hubungi pengurus RT untuk konfirmasi data kamu.</p>
    @else
        <h1 class="text-xl font-bold mb-2 text-blue-700">Akun Menunggu Verifikasi</h1>
        <p class="text-gray-600">Terima kasih sudah mendaftar. Pengurus RT sedang memeriksa data kamu. Silakan masuk lagi setelah akun disetujui.</p>
    @endif
    <form method="POST" action="{{ route('logout') }}" class="mt-6">
        @csrf
        <button class="underline text-gray-600">Keluar</button>
    </form>
</x-guest-layout>