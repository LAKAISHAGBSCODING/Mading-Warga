<x-mading-layout title="Forum warga" subtitle="Ngobrol, bertanya, dan saling bantu.">
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
        <x-kosong text="Belum ada postingan." />
    @endforelse

    {{ $posts->links() }}
</x-mading-layout>