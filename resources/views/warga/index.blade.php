<x-mading-layout title="Verifikasi Warga">
    <div class="grid grid-cols-3 gap-2 text-center text-base">
        @foreach(['pending' => 'Menunggu', 'disetujui' => 'Disetujui', 'ditolak' => 'Ditolak'] as $k => $label)
            <a href="{{ route('warga.index', ['status' => $k]) }}"
               class="rounded-xl py-2 font-semibold {{ $status === $k ? 'bg-blue-600 text-white' : 'bg-white text-blue-700 border border-blue-200' }}">
                {{ $label }}
            </a>
        @endforeach
    </div>

    @forelse($warga as $w)
        <div class="card space-y-3">
            <div>
                <b class="text-xl text-blue-900">{{ $w->name }}</b>
                <p>🏠 {{ $w->no_rumah }}</p>
                <p>📱 {{ $w->no_wa }}</p>
                <p class="text-sm text-gray-500">Daftar {{ $w->created_at->translatedFormat('j F Y') }}</p>
            </div>

            <a href="https://wa.me/{{ $w->no_wa }}" target="_blank" class="btn-wa w-full">💬 Cek lewat WhatsApp</a>

            <form method="POST" action="{{ route('warga.update', $w) }}" class="grid grid-cols-2 gap-2">
                @csrf
                @method('PATCH')
                @if($w->status_verifikasi !== 'disetujui')
                    <button name="status" value="disetujui" class="btn-primary">✅ Setujui</button>
                @endif
                @if($w->status_verifikasi !== 'ditolak')
                    <button name="status" value="ditolak" class="btn-danger">❌ Tolak</button>
                @endif
            </form>
        </div>
    @empty
        <div class="card text-center text-gray-500">Tidak ada data.</div>
    @endforelse
</x-mading-layout>