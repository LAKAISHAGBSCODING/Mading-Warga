<x-mading-layout title="Lapak Warga">
    <a href="{{ route('lapak.create') }}" class="btn-primary w-full">➕ Pasang Dagangan / Jasa</a>

    @forelse($items as $l)
        <div class="card space-y-3">
            @if($l->foto)
                <img src="{{ asset('storage/'.$l->foto) }}" alt="{{ $l->nama }}" class="rounded-xl w-full max-h-72 object-cover">
            @endif
            <h2 class="text-xl font-bold text-blue-900">{{ $l->nama }}</h2>
            <p class="text-2xl font-bold text-blue-600">Rp {{ number_format($l->harga, 0, ',', '.') }}</p>
            <p class="whitespace-pre-line">{{ $l->deskripsi }}</p>
            <p class="text-sm text-gray-500">Penjual: {{ $l->user->name }} · 🏠 {{ $l->user->no_rumah }}</p>

            <a target="_blank"
               href="https://wa.me/{{ $l->user->no_wa }}?text={{ urlencode('Halo, saya lihat '.$l->nama.' di Lapak Warga. Masih tersedia?') }}"
               class="btn-wa w-full">💬 Chat WhatsApp</a>

            @if(auth()->user()->isAdmin() || auth()->id() === $l->user_id)
                <x-hapus :action="route('lapak.destroy', $l)" />
            @endif
        </div>
    @empty
        <div class="card text-center text-gray-500">Belum ada lapak.</div>
    @endforelse

    {{ $items->links() }}
</x-mading-layout>