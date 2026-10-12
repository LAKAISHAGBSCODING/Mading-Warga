<x-mading-layout>
    <a href="{{ route('forum.index') }}" class="inline-flex items-center gap-1.5 font-semibold text-blue-700">
        <x-ic name="back" class="h-5 w-5" /> Kembali ke forum
    </a>

    <article class="card space-y-3">
        <span class="chip">{{ $post->label }}</span>
        <h1 class="text-2xl font-extrabold leading-snug text-slate-900">{{ $post->judul }}</h1>
        <div class="flex items-center gap-2 meta">
            <span class="grid place-items-center h-7 w-7 rounded-full bg-blue-100 text-blue-700 text-xs font-bold">{{ Str::upper(Str::substr($post->user->name, 0, 1)) }}</span>
            {{ $post->user->name }} · {{ $post->created_at->diffForHumans() }}
        </div>
        <p class="whitespace-pre-line text-slate-700 leading-relaxed">{{ $post->isi }}</p>

        @if(auth()->user()->isAdmin() || auth()->id() === $post->user_id)
            <div class="border-t border-slate-100 pt-4">
                <x-hapus :action="route('forum.destroy', $post)" />
            </div>
        @endif
    </article>

    <h2 class="text-lg font-bold text-slate-900">Balasan ({{ $post->comments->count() }})</h2>

    @forelse($post->comments as $c)
        <div class="card space-y-2">
            <div class="flex items-center gap-2">
                <span class="grid place-items-center h-8 w-8 rounded-full bg-slate-100 text-slate-700 text-sm font-bold">{{ Str::upper(Str::substr($c->user->name, 0, 1)) }}</span>
                <p class="font-semibold text-slate-900">{{ $c->user->name }}</p>
                <span class="meta">· {{ $c->created_at->diffForHumans() }}</span>
            </div>
            <p class="whitespace-pre-line text-slate-700 leading-relaxed">{{ $c->isi }}</p>
            @if(auth()->user()->isAdmin() || auth()->id() === $c->user_id)
                <form method="POST" action="{{ route('komentar.destroy', $c) }}" onsubmit="return confirm('Hapus balasan ini?')">
                    @csrf @method('DELETE')
                    <button class="text-sm font-semibold text-red-600">Hapus balasan</button>
                </form>
            @endif
        </div>
    @empty
        <x-kosong text="Belum ada balasan. Jadilah yang pertama." />
    @endforelse

    <form method="POST" action="{{ route('forum.komentar', $post) }}" class="card space-y-3">
        @csrf
        <label class="lbl">Tulis balasan</label>
        <textarea name="isi" rows="3" class="field" required></textarea>
        <button class="btn-primary w-full"><x-ic name="send" class="h-5 w-5" /> Kirim balasan</button>
    </form>
</x-mading-layout>