<x-mading-layout title="Forum warga" subtitle="Ngobrol, bertanya, dan saling bantu.">
    <form method="GET" action="{{ route('forum.index') }}" class="relative">
        <x-ic name="search" class="pointer-events-none absolute left-4 top-1/2 h-5 w-5 -translate-y-1/2 text-slate-400" />
        <input type="search" name="q" value="{{ $q }}" placeholder="Cari judul, isi, atau nama warga..."
               class="field !pl-12 !pr-28 [&::-webkit-search-cancel-button]:hidden" enterkeyhint="search">
        @if($q !== '')
            <a href="{{ route('forum.index') }}" aria-label="Hapus pencarian"
               class="absolute right-20 top-1/2 grid h-9 w-9 -translate-y-1/2 place-items-center rounded-full text-slate-400 hover:bg-slate-100 hover:text-slate-700">
                <x-ic name="x" class="h-5 w-5" />
            </a>
        @endif
        <button class="absolute right-2 top-1/2 -translate-y-1/2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">Cari</button>
    </form>

    <a href="{{ route('forum.create') }}" class="btn-primary w-full">
        <x-ic name="plus" class="h-5 w-5" /> Buat postingan
    </a>

    @forelse($posts as $p)
        <div class="card space-y-3">
            <div class="flex items-center gap-3">
                <x-avatar :user="$p->user" size="h-11 w-11" />
                <div class="min-w-0">
                    <p class="font-bold leading-tight text-slate-900">{{ $p->user->name }}</p>
                    <p class="meta">{{ $p->created_at->diffForHumans() }}</p>
                </div>
                <span class="chip ml-auto shrink-0">{{ $p->label }}</span>
            </div>

            <a href="{{ route('forum.show', $p) }}" class="block space-y-1">
                <h2 class="text-lg font-bold leading-snug text-slate-900">{{ $p->judul }}</h2>
                <p class="line-clamp-2 text-slate-600">{{ $p->isi }}</p>
            </a>

            <a href="{{ route('forum.show', $p) }}#form-komentar"
               class="btn-outline w-full">
                <x-ic name="chat" class="h-5 w-5" />
                {{ $p->comments_count > 0 ? $p->comments_count.' komentar · Tulis komentar' : 'Tulis komentar' }}
            </a>
        </div>
    @empty
        <x-kosong :text="$q !== '' ? 'Tidak ada postingan yang cocok dengan pencarianmu.' : 'Belum ada postingan.'" />
    @endforelse

    {{ $posts->links() }}
</x-mading-layout>