<x-mading-layout title="Halo, {{ Str::words(auth()->user()->name, 1, '') }} 👋">

    @if(auth()->user()->isAdmin())
        <a href="{{ route('warga.index') }}" class="card block border-orange-300 bg-orange-50">
            <b>🔑 Kelola Warga</b>
            <p>{{ $jumlahPending }} akun menunggu persetujuan</p>
        </a>
    @endif

    <h2 class="text-xl font-bold text-blue-800">📢 Pengumuman Terbaru</h2>
    @forelse($pengumumans as $p)
        <a href="{{ route('pengumuman.index') }}" class="card block">
            <b class="text-blue-900">{{ $p->judul }}</b>
            <p class="text-sm text-gray-500">{{ $p->created_at->translatedFormat('j F Y') }}</p>
        </a>
    @empty
        <div class="card text-gray-500">Belum ada pengumuman.</div>
    @endforelse

    <h2 class="text-xl font-bold text-blue-800 pt-2">📅 Acara Mendatang</h2>
    @forelse($acaras as $a)
        <a href="{{ route('acara.index') }}" class="card block">
            <b class="text-blue-900">{{ $a->judul }}</b>
            <p>{{ $a->tanggal->translatedFormat('l, j F Y') }} · {{ substr($a->jam, 0, 5) }} WIB</p>
            <p class="text-gray-500">📍 {{ $a->lokasi }}</p>
        </a>
    @empty
        <div class="card text-gray-500">Belum ada acara.</div>
    @endforelse
</x-mading-layout>