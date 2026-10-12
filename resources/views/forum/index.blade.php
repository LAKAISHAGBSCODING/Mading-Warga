<x-mading-layout title="Forum Warga">
    <a href="{{ route('forum.create') }}" class="btn-primary w-full">➕ Buat Postingan</a>

    @forelse($posts as $p)
        <a href="{{ route('forum.show', $p) }}" class="card block space-y-1">
            <span class="inline-block bg-blue-100 text-blue-800 text-sm font-semibold rounded-full px-3 py-1">[{{ $p->label }}]</span>
            <h2 class="text-lg font-bold text-blue-900">{{ $p->judul }}</h2>
            <p class="text-sm text-gray-500">
                {{ $p->user->name }} · {{ $p->created_at->diffForHumans() }} · 💬 {{ $p->comments_count }}
            </p>
        </a>
    @empty
        <div class="card text-center text-gray-500">Belum ada postingan.</div>
    @endforelse

    {{ $posts->links() }}
</x-mading-layout>