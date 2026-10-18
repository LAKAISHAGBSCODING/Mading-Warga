<x-mading-layout title="Pengumuman" subtitle="Informasi resmi dari pengurus RT.">
    <form method="GET" action="{{ route('pengumuman.index') }}" class="relative">
        <x-ic name="search" class="pointer-events-none absolute left-4 top-1/2 h-5 w-5 -translate-y-1/2 text-slate-400" />
        <input type="search" name="q" value="{{ $q }}" placeholder="Cari pengumuman..."
               class="field !pl-12 !pr-28 [&::-webkit-search-cancel-button]:hidden" enterkeyhint="search">
        @if($q !== '')
            <a href="{{ route('pengumuman.index') }}" aria-label="Hapus pencarian"
               class="absolute right-20 top-1/2 grid h-9 w-9 -translate-y-1/2 place-items-center rounded-full text-slate-400 hover:bg-slate-100 hover:text-slate-700">
                <x-ic name="x" class="h-5 w-5" />
            </a>
        @endif
        <button class="absolute right-2 top-1/2 -translate-y-1/2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">Cari</button>
    </form>

    @if(auth()->user()->isAdmin())
        <a href="{{ route('pengumuman.create') }}" class="btn-primary w-full">
            <x-ic name="plus" class="h-5 w-5" /> Buat pengumuman
        </a>
    @endif

    @forelse($items as $p)
        <article class="card space-y-3">
            <div class="flex items-center justify-between gap-3">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="chip">Pengumuman</span>
                    @if($p->is_pinned)
                        <span class="inline-flex items-center gap-1 rounded-full bg-amber-100 px-3 py-1 text-sm font-bold text-amber-800">
                            <x-ic name="pin" class="h-4 w-4" /> Disematkan
                        </span>
                    @elseif(!auth()->user()->isAdmin() && $p->sudah_dibaca_count === 0)
                        <span class="rounded-full bg-red-100 px-3 py-1 text-sm font-bold text-red-700">Baru</span>
                    @endif
                </div>
                <span class="meta">{{ $p->created_at->translatedFormat('j F Y') }}</span>
            </div>
            <a href="{{ route('pengumuman.show', $p) }}" class="block space-y-2">
                <h2 class="text-xl font-extrabold leading-snug text-slate-900">{{ $p->judul }}</h2>
                <p class="whitespace-pre-line text-slate-700 leading-relaxed">{{ Str::limit($p->isi, 240) }}</p>
            </a>

            <div class="flex items-center justify-between gap-3">
                <p class="meta">Oleh {{ $p->user->name }}</p>
                <a href="{{ route('pengumuman.show', $p) }}" class="text-sm font-semibold text-blue-700">Baca selengkapnya</a>
            </div>

            @if(auth()->user()->isAdmin())
                <div class="space-y-3 border-t border-slate-100 pt-4">
                    <p class="meta">Dibaca {{ $p->dibaca_warga_count }} dari {{ $totalWarga }} warga</p>
                    <div class="grid grid-cols-3 gap-2">
                        <a href="{{ route('pengumuman.show', $p) }}" class="btn-outline text-sm">Detail</a>
                        <a href="{{ route('pengumuman.edit', $p) }}" class="btn-outline text-sm">
                            <x-ic name="pencil" class="h-4 w-4" /> Edit
                        </a>
                        <form method="POST" action="{{ route('pengumuman.pin', $p) }}">
                            @csrf
                            @method('PATCH')
                            <button class="btn-outline w-full text-sm">{{ $p->is_pinned ? 'Lepas pin' : 'Sematkan' }}</button>
                        </form>
                    </div>
                    <x-hapus :action="route('pengumuman.destroy', $p)" />
                </div>
            @else
                <a href="{{ route('pengumuman.show', $p) }}" class="btn-outline w-full">Baca pengumuman</a>
            @endif
        </article>
    @empty
        <x-kosong :text="$q !== '' ? 'Tidak ada pengumuman yang cocok dengan pencarianmu.' : 'Belum ada pengumuman.'" />
    @endforelse

    {{ $items->links() }}
</x-mading-layout>