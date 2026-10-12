<x-mading-layout>
    <a href="{{ route('forum.index') }}" class="text-blue-700 underline">← Kembali ke forum</a>

    <article class="card space-y-2">
        <span class="inline-block bg-blue-100 text-blue-800 text-sm font-semibold rounded-full px-3 py-1">[{{ $post->label }}]</span>
        <h1 class="text-2xl font-bold text-blue-900">{{ $post->judul }}</h1>
        <p class="text-sm text-gray-500">{{ $post->user->name }} · {{ $post->created_at->diffForHumans() }}</p>
        <p class="whitespace-pre-line">{{ $post->isi }}</p>

        @if(auth()->user()->isAdmin() || auth()->id() === $post->user_id)
            <x-hapus :action="route('forum.destroy', $post)" />
        @endif
    </article>

    <h2 class="text-xl font-bold text-blue-800">💬 Balasan ({{ $post->comments->count() }})</h2>
    @foreach($post->comments as $c)
        <div class="card space-y-1">
            <p class="font-semibold text-blue-900">{{ $c->user->name }}
                <span class="text-sm text-gray-500 font-normal">· {{ $c->created_at->diffForHumans() }}</span>
            </p>
            <p class="whitespace-pre-line">{{ $c->isi }}</p>
            @if(auth()->user()->isAdmin() || auth()->id() === $c->user_id)
                <form method="POST" action="{{ route('komentar.destroy', $c) }}" onsubmit="return confirm('Hapus balasan ini?')">
                    @csrf @method('DELETE')
                    <button class="text-red-600 underline text-sm">Hapus</button>
                </form>
            @endif
        </div>
    @endforeach

    <form method="POST" action="{{ route('forum.komentar', $post) }}" class="card space-y-3">
        @csrf
        <label class="lbl">Tulis balasan</label>
        <textarea name="isi" rows="3" class="field" required></textarea>
        <button class="btn-primary w-full">📨 Kirim Balasan</button>
    </form>
</x-mading-layout>