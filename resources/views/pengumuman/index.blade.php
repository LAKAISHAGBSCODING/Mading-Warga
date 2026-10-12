<x-mading-layout title="Pengumuman" subtitle="Informasi resmi dari pengurus RT.">
    @if(auth()->user()->isAdmin())
        <a href="{{ route('pengumuman.create') }}" class="btn-primary w-full">
            <x-ic name="plus" class="h-5 w-5" /> Buat pengumuman
        </a>
    @endif

    @forelse($items as $p)
        <article class="card space-y-3">
            <div class="flex items-center justify-between gap-3">
                <span class="chip">Pengumuman</span>
                <span class="meta">{{ $p->created_at->translatedFormat('j F Y') }}</span>
            </div>
            <h2 class="text-xl font-extrabold leading-snug text-slate-900">{{ $p->judul }}</h2>
            <p class="whitespace-pre-line text-slate-700 leading-relaxed">{{ $p->isi }}</p>

            @if($p->lampiran)
                @if(Str::endsWith($p->lampiran, '.pdf'))
                    <a href="{{ asset('storage/'.$p->lampiran) }}" target="_blank" class="btn-outline w-full">
                        <x-ic name="clip" class="h-5 w-5" /> Buka lampiran (PDF)
                    </a>
                @else
                    <img src="{{ asset('storage/'.$p->lampiran) }}" class="w-full rounded-xl border border-slate-200" alt="Lampiran">
                @endif
            @endif

            <p class="meta">Oleh {{ $p->user->name }}</p>

            @if(auth()->user()->isAdmin())
                <div class="grid grid-cols-2 gap-2 border-t border-slate-100 pt-4">
                    <a href="{{ route('pengumuman.edit', $p) }}" class="btn-outline">
                        <x-ic name="pencil" class="h-5 w-5" /> Edit
                    </a>
                    <x-hapus :action="route('pengumuman.destroy', $p)" />
                </div>
            @endif
        </article>
    @empty
        <x-kosong text="Belum ada pengumuman." />
    @endforelse

    {{ $items->links() }}
</x-mading-layout>