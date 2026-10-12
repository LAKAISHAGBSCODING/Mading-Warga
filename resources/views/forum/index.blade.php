<x-mading-layout title="Forum warga" subtitle="Ngobrol, bertanya, dan saling bantu.">
    <a href="{{ route('forum.create') }}" class="btn-primary w-full">
        <x-ic name="plus" class="h-5 w-5" /> Buat postingan
    </a>

    @forelse($posts as $p)
        <a href="{{ route('forum.show', $p) }}" class="card block space-y-2 hover:border-blue-300 transition">
            <span class="chip">{{ $p->label }}</span>
            <h2 class="text-lg font-bold leading-snug text-slate-900">{{ $p->judul }}</h2>
            <p class="line-clamp-2 text-slate-600">{{ $p->isi }}</p>
            <div class="flex items-center gap-3 pt-1 meta">
                <span class="grid place-items-center h-6 w-6 rounded-full bg-blue-100 text-blue-700 text-xs font-bold">{{ Str::upper(Str::substr($p->user->name, 0, 1)) }}</span>
                <span>{{ $p->user->name }}</span>
                <span>{{ $p->created_at->diffForHumans() }}</span>
                <span class="ml-auto flex items-center gap-1"><x-ic name="chat" class="h-4 w-4" /> {{ $p->comments_count }}</span>
            </div>
        </a>
    @empty
        <x-kosong text="Belum ada postingan." />
    @endforelse

    {{ $posts->links() }}
</x-mading-layout>