<x-mading-layout title="Pengumuman RT">
    @if(auth()->user()->isAdmin())
        <a href="{{ route('pengumuman.create') }}" class="btn-primary w-full">➕ Buat Pengumuman</a>
    @endif

    @forelse($items as $p)
        <article class="card space-y-3">
            <h2 class="text-xl font-bold text-blue-900">{{ $p->judul }}</h2>
            <p class="text-sm text-gray-500">{{ $p->created_at->translatedFormat('j F Y') }} · {{ $p->user->name }}</p>
            <p class="whitespace-pre-line">{{ $p->isi }}</p>

            @if($p->lampiran)
                @if(Str::endsWith($p->lampiran, '.pdf'))
                    <a href="{{ asset('storage/'.$p->lampiran) }}" target="_blank" class="btn-outline w-full">📎 Buka Lampiran (PDF)</a>
                @else
                    <img src="{{ asset('storage/'.$p->lampiran) }}" class="rounded-xl w-full" alt="Lampiran">
                @endif
            @endif

            @if(auth()->user()->isAdmin())
                <div class="grid grid-cols-2 gap-2">
                    <a href="{{ route('pengumuman.edit', $p) }}" class="btn-outline">✏️ Edit</a>
                    <x-hapus :action="route('pengumuman.destroy', $p)" />
                </div>
            @endif
        </article>
    @empty
        <div class="card text-center text-gray-500">Belum ada pengumuman.</div>
    @endforelse

    {{ $items->links() }}
</x-mading-layout>