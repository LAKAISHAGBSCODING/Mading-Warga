<x-mading-layout title="Lapak warga" subtitle="Usaha dan jasa tetangga sendiri.">

        <form method="GET" action="{{ route('lapak.index') }}" class="relative">
        <x-ic name="search" class="pointer-events-none absolute left-4 top-1/2 h-5 w-5 -translate-y-1/2 text-slate-400" />
        <input type="search" name="q" value="{{ $q }}" placeholder="Cari kopi, servis, nama penjual..."
               class="field !pl-12 !pr-28 [&::-webkit-search-cancel-button]:hidden" enterkeyhint="search">

        @if($q !== '')
            <a href="{{ route('lapak.index') }}" aria-label="Hapus pencarian"
               class="absolute right-20 top-1/2 grid h-9 w-9 -translate-y-1/2 place-items-center rounded-full text-slate-400 hover:bg-slate-100 hover:text-slate-700">
                <x-ic name="x" class="h-5 w-5" />
            </a>
        @endif

        <button class="absolute right-2 top-1/2 -translate-y-1/2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">
            Cari
        </button>
    </form>



    <a href="{{ route('lapak.create') }}" class="btn-primary w-full">
        <x-ic name="plus" class="h-5 w-5" /> Pasang dagangan atau jasa
    </a>

    @forelse($items as $l)
      <div id="lapak-{{ $l->id }}" class="card scroll-mt-20 !p-0 overflow-hidden">
   @if($l->foto)
    <div class="relative">
        <img src="{{ asset('storage/'.$l->foto) }}" alt="{{ $l->nama }}"
             class="aspect-[4/3] w-full object-cover {{ $l->tersedia ? '' : 'grayscale opacity-60' }}">
        <span class="absolute left-3 top-3 rounded-full px-3 py-1 text-sm font-bold shadow-sm
                     {{ $l->tersedia ? 'bg-emerald-600 text-white' : 'bg-slate-800 text-white' }}">
            {{ $l->tersedia ? 'Tersedia' : 'Habis' }}
        </span>
    </div>
@endif
            <div class="space-y-3 p-5">
                @unless($l->foto)
                    <span class="inline-flex rounded-full px-3 py-1 text-sm font-bold
                                 {{ $l->tersedia ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-200 text-slate-700' }}">
                        {{ $l->tersedia ? 'Tersedia' : 'Habis' }}
                    </span>
                @endunless
                <div>
                    <h2 class="text-xl font-extrabold leading-snug text-slate-900">{{ $l->nama }}</h2>
                    <p class="mt-1 text-2xl font-extrabold text-blue-600">Rp {{ number_format($l->harga, 0, ',', '.') }}</p>
                </div>
                <p class="whitespace-pre-line leading-relaxed text-slate-700">{{ $l->deskripsi }}</p>

                <div class="flex items-center gap-3 rounded-xl bg-slate-50 p-3">
                    <x-avatar :user="$l->user" size="h-10 w-10" />
                    <div class="min-w-0">
                        <p class="font-semibold leading-tight text-slate-900">{{ $l->user->name }}</p>
                        <p class="meta">Rumah {{ $l->user->no_rumah }}</p>
                    </div>
                </div>

                @if($l->tersedia)
    <a target="_blank"
       href="https://wa.me/{{ $l->user->no_wa }}?text={{ urlencode('Halo, saya lihat '.$l->nama.' di Lapak Warga. Masih tersedia?') }}"
       class="btn-wa w-full">
        <x-ic name="chat" class="h-5 w-5" /> Chat WhatsApp
    </a>
@else
    <div class="grid min-h-[52px] place-items-center rounded-xl bg-slate-100 px-5 py-3 font-semibold text-slate-500">
        Stok sedang habis
    </div>
@endif

@if(auth()->user()->isAdmin() || auth()->id() === $l->user_id)
    <div class="space-y-2 border-t border-slate-100 pt-4">
        <form method="POST" action="{{ route('lapak.stok', ['lapak' => $l, 'q' => $q, 'page' => $items->currentPage()]) }}">
            @csrf
            @method('PATCH')
            <button class="{{ $l->tersedia ? 'btn-outline' : 'btn-primary' }} w-full">
                <x-ic name="{{ $l->tersedia ? 'x' : 'check' }}" class="h-5 w-5" />
                {{ $l->tersedia ? 'Tandai habis' : 'Tandai tersedia lagi' }}
            </button>
        </form>
        <x-hapus :action="route('lapak.destroy', $l)" judul="Hapus lapak ini?" teks="Lapak dan fotonya akan dihapus permanen." />
    </div>
@endif
            </div>
        </div>
    @empty
        <x-kosong :text="$q !== '' ? 'Tidak ada lapak yang cocok dengan pencarianmu.' : 'Belum ada lapak.'" />
    @endforelse

    {{ $items->links() }}
</x-mading-layout>