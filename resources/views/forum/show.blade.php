<x-mading-layout>
    <a href="{{ route('forum.index') }}" class="inline-flex items-center gap-1.5 font-semibold text-blue-700">
        <x-ic name="back" class="h-5 w-5" /> Kembali ke forum
    </a>

    <article class="card space-y-4">
        <div class="flex items-center gap-3">
            <x-avatar :user="$post->user" size="h-12 w-12" text="text-lg" />
            <div class="min-w-0">
                <p class="font-bold leading-tight text-slate-900">{{ $post->user->name }}</p>
                <p class="meta">{{ $post->created_at->diffForHumans() }}</p>
            </div>
            <span class="chip ml-auto shrink-0">{{ $post->label }}</span>
        </div>

        <h1 class="text-2xl font-extrabold leading-snug text-slate-900">{{ $post->judul }}</h1>
        <p class="whitespace-pre-line leading-relaxed text-slate-700">{{ $post->isi }}</p>

        <a href="#form-komentar" class="btn-primary w-full">
            <x-ic name="chat" class="h-5 w-5" /> Tulis komentar
        </a>

        @if(auth()->user()->isAdmin() || auth()->id() === $post->user_id)
            <div class="border-t border-slate-100 pt-4">
                <x-hapus :action="route('forum.destroy', $post)" judul="Hapus postingan?" teks="Postingan dan semua komentarnya akan ikut terhapus." />
            </div>
        @endif
    </article>

    <h2 id="daftar-komentar" class="scroll-mt-20 text-lg font-bold text-slate-900">
        Komentar ({{ $post->comments->count() }})
    </h2>

    @forelse($post->comments as $c)
        <div id="komentar-{{ $c->id }}" class="card scroll-mt-20 space-y-2">
            <div class="flex items-center gap-3">
                <x-avatar :user="$c->user" size="h-10 w-10" />
                <div class="min-w-0">
                    <p class="font-semibold leading-tight text-slate-900">{{ $c->user->name }}</p>
                    <p class="meta">{{ $c->created_at->diffForHumans() }}</p>
                </div>
            </div>
            <p class="whitespace-pre-line leading-relaxed text-slate-700">{{ $c->isi }}</p>
            @if(auth()->user()->isAdmin() || auth()->id() === $c->user_id)
                <form method="POST" action="{{ route('komentar.destroy', $c) }}"
      data-konfirm="Komentar ini akan dihapus dan tidak bisa dikembalikan."
      data-konfirm-judul="Hapus komentar?">
    @csrf @method('DELETE')
    <button class="inline-flex items-center gap-1.5 rounded-lg px-3 py-2 text-sm font-semibold text-red-600 hover:bg-red-50">
        <x-ic name="trash" class="h-4 w-4" /> Hapus komentar
    </button>
</form>
            @endif
        </div>
    @empty
        <x-kosong text="Belum ada komentar. Jadilah yang pertama." />
    @endforelse

    <form id="form-komentar" method="POST" action="{{ route('forum.komentar', $post) }}"
          class="card scroll-mt-20 space-y-3 border-2 border-blue-200 bg-blue-50/40">
        @csrf
        <div class="flex items-center gap-3">
            <x-avatar :user="auth()->user()" size="h-10 w-10" />
            <label for="isi-komentar" class="text-lg font-bold text-slate-900">Tulis komentar</label>
        </div>
        <textarea id="isi-komentar" name="isi" rows="4" class="field" placeholder="Ketik komentar kamu di sini..." required></textarea>
        <button class="btn-primary w-full text-lg">
            <x-ic name="send" class="h-5 w-5" /> Kirim komentar
        </button>
    </form>
</x-mading-layout>