<x-mading-layout title="Lapak warga" subtitle="Usaha dan jasa tetangga sendiri.">
    <a href="{{ route('lapak.create') }}" class="btn-primary w-full">
        <x-ic name="plus" class="h-5 w-5" /> Pasang dagangan atau jasa
    </a>

    @forelse($items as $l)
        <div class="card !p-0 overflow-hidden">
            @if($l->foto)
                <img src="{{ asset('storage/'.$l->foto) }}" alt="{{ $l->nama }}" class="aspect-[4/3] w-full object-cover">
            @endif
            <div class="space-y-3 p-5">
                <div>
                    <h2 class="text-xl font-extrabold leading-snug text-slate-900">{{ $l->nama }}</h2>
                    <p class="mt-1 text-2xl font-extrabold text-blue-600">Rp {{ number_format($l->harga, 0, ',', '.') }}</p>
                </div>
                <p class="whitespace-pre-line text-slate-700 leading-relaxed">{{ $l->deskripsi }}</p>
                <p class="meta flex items-center gap-1.5">
                    <x-ic name="home" class="h-4 w-4" /> {{ $l->user->name }} · {{ $l->user->no_rumah }}
                </p>

                <a target="_blank"
                   href="https://wa.me/{{ $l->user->no_wa }}?text={{ urlencode('Halo, saya lihat '.$l->nama.' di Lapak Warga. Masih tersedia?') }}"
                   class="btn-wa w-full">
                    <x-ic name="chat" class="h-5 w-5" /> Chat WhatsApp
                </a>

                @if(auth()->user()->isAdmin() || auth()->id() === $l->user_id)
                    <x-hapus :action="route('lapak.destroy', $l)" />
                @endif
            </div>
        </div>
    @empty
        <x-kosong text="Belum ada lapak." />
    @endforelse

    {{ $items->links() }}
</x-mading-layout>