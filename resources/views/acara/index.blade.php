<x-mading-layout title="Agenda Warga">
    <a href="{{ route('acara.create') }}" class="btn-primary w-full">
        ➕ {{ auth()->user()->isAdmin() ? 'Buat Acara' : 'Usulkan Acara' }}
    </a>

    @if($menunggu->count())
        <h2 class="text-xl font-bold text-orange-600">⏳ Usulan Menunggu Persetujuan</h2>
        @foreach($menunggu as $a)
            <div class="card border-orange-300 space-y-2">
                <b class="text-lg">{{ $a->judul }}</b>
                <p>Diusulkan oleh {{ $a->pengaju->name }}</p>
                <p>{{ $a->tanggal->translatedFormat('j F Y') }}, {{ substr($a->jam, 0, 5) }} · 📍 {{ $a->lokasi }}</p>
                <a href="{{ route('acara.edit', $a) }}" class="btn-primary w-full">Tinjau & Putuskan</a>
            </div>
        @endforeach
    @endif

    @if($usulanSaya->count())
        <h2 class="text-xl font-bold text-blue-800">Usulan Saya</h2>
        @foreach($usulanSaya as $a)
            <div class="card">
                <b>{{ $a->judul }}</b>
                <p>Status:
                    <span class="font-semibold {{ $a->status === 'ditolak' ? 'text-red-600' : 'text-orange-600' }}">
                        {{ $a->status === 'ditolak' ? 'Ditolak' : 'Menunggu persetujuan' }}
                    </span>
                </p>
            </div>
        @endforeach
    @endif

    <h2 class="text-xl font-bold text-blue-800">📅 Acara Mendatang</h2>
    @forelse($acaras as $a)
        @php
            $saya = $a->rsvps->firstWhere('user_id', auth()->id());
            $hadir = $a->rsvps->where('status', 'hadir');
            $tidak = $a->rsvps->where('status', 'tidak_hadir');
        @endphp
        <div class="card space-y-3">
            <h3 class="text-xl font-bold text-blue-900">{{ $a->judul }}</h3>
            <p>🗓️ {{ $a->tanggal->translatedFormat('l, j F Y') }}, {{ substr($a->jam, 0, 5) }} WIB</p>
            <p>📍 {{ $a->lokasi }}</p>
            @if($a->deskripsi)
                <p class="whitespace-pre-line text-gray-600">{{ $a->deskripsi }}</p>
            @endif
            <p class="text-blue-700 font-semibold">✅ {{ $hadir->count() }} warga akan hadir</p>

            <form method="POST" action="{{ route('acara.rsvp', $a) }}" class="grid grid-cols-2 gap-2">
                @csrf
                <button name="status" value="hadir"
                        class="{{ $saya?->status === 'hadir' ? 'btn-primary' : 'btn-outline' }}">✅ Hadir</button>
                <button name="status" value="tidak_hadir"
                        class="{{ $saya?->status === 'tidak_hadir' ? 'btn-danger' : 'btn-outline' }}">❌ Tidak Hadir</button>
            </form>

            @if(auth()->user()->isAdmin())
                <details class="bg-blue-50 rounded-xl p-3">
                    <summary class="font-semibold cursor-pointer">Rekap kehadiran</summary>
                    <p class="mt-2 font-semibold">Hadir ({{ $hadir->count() }})</p>
                    <ul class="list-disc pl-5">
                        @forelse($hadir as $r)<li>{{ $r->user->name }} ({{ $r->user->no_rumah }})</li>@empty<li>-</li>@endforelse
                    </ul>
                    <p class="mt-2 font-semibold">Tidak hadir ({{ $tidak->count() }})</p>
                    <ul class="list-disc pl-5">
                        @forelse($tidak as $r)<li>{{ $r->user->name }}</li>@empty<li>-</li>@endforelse
                    </ul>
                </details>
                <div class="grid grid-cols-2 gap-2">
                    <a href="{{ route('acara.edit', $a) }}" class="btn-outline">✏️ Edit</a>
                    <x-hapus :action="route('acara.destroy', $a)" />
                </div>
            @endif
        </div>
    @empty
        <div class="card text-center text-gray-500">Belum ada acara.</div>
    @endforelse
</x-mading-layout>