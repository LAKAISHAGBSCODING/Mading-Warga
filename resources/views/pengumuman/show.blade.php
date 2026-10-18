<x-mading-layout :title="$pengumuman->judul" subtitle="Pengumuman resmi dari pengurus RT.">
    <a href="{{ route('pengumuman.index') }}" class="inline-flex items-center gap-1.5 font-semibold text-blue-700">
        <x-ic name="back" class="h-5 w-5" /> Kembali ke pengumuman
    </a>

    <article class="card space-y-4">
        <div class="flex flex-wrap items-center gap-2">
            <span class="chip">{{ $pengumuman->created_at->translatedFormat('j F Y') }}</span>
            @if($pengumuman->is_pinned)
                <span class="inline-flex items-center gap-1 rounded-full bg-amber-100 px-3 py-1 text-sm font-bold text-amber-800">
                    <x-ic name="pin" class="h-4 w-4" /> Disematkan
                </span>
            @endif
        </div>
        <h2 class="text-2xl font-extrabold leading-snug text-slate-900">{{ $pengumuman->judul }}</h2>
        <p class="whitespace-pre-line leading-relaxed text-slate-700">{{ $pengumuman->isi }}</p>

        @if($pengumuman->lampiran)
            @if(Str::endsWith(strtolower($pengumuman->lampiran), '.pdf'))
                <a href="{{ asset('storage/'.$pengumuman->lampiran) }}" target="_blank" rel="noopener" class="btn-outline w-full">
                    <x-ic name="clip" class="h-5 w-5" /> Buka lampiran (PDF)
                </a>
            @else
                <img src="{{ asset('storage/'.$pengumuman->lampiran) }}" class="w-full rounded-xl border border-slate-200" alt="Lampiran pengumuman">
            @endif
        @endif

        <p class="meta">Diterbitkan oleh {{ $pengumuman->user->name }}</p>

        @if(auth()->user()->isAdmin())
            <section class="space-y-3 border-t border-slate-100 pt-4">
                <h3 class="font-bold text-slate-900">Status dibaca</h3>
                <p class="text-sm text-slate-600">Dibaca {{ $pembacaList->count() }} dari {{ $totalWarga }} warga yang disetujui.</p>
                <details class="rounded-xl bg-slate-50 p-4">
                    <summary class="cursor-pointer font-semibold text-slate-800">Lihat daftar warga</summary>
                    <h4 class="mt-3 font-semibold text-slate-900">Sudah membaca ({{ $pembacaList->count() }})</h4>
                    <ul class="mt-1 list-disc space-y-1 pl-5 text-sm text-slate-700">
                        @forelse($pembacaList as $pembaca)
                            <li>{{ $pembaca->name }} — Rumah {{ $pembaca->no_rumah }}</li>
                        @empty
                            <li>Belum ada warga yang membaca.</li>
                        @endforelse
                    </ul>
                    <h4 class="mt-4 font-semibold text-slate-900">Belum membaca ({{ $belumMembaca->count() }})</h4>
                    <ul class="mt-1 list-disc space-y-1 pl-5 text-sm text-slate-700">
                        @forelse($belumMembaca as $warga)
                            <li>{{ $warga->name }} — Rumah {{ $warga->no_rumah }}</li>
                        @empty
                            <li>Semua warga sudah membaca.</li>
                        @endforelse
                    </ul>
                </details>
            </section>
            <div class="grid grid-cols-2 gap-2 border-t border-slate-100 pt-4">
                <a href="{{ route('pengumuman.edit', $pengumuman) }}" class="btn-outline">
                    <x-ic name="pencil" class="h-5 w-5" /> Edit
                </a>
                <form method="POST" action="{{ route('pengumuman.pin', $pengumuman) }}">
                    @csrf
                    @method('PATCH')
                    <button class="btn-outline w-full">{{ $pengumuman->is_pinned ? 'Lepas sematan' : 'Sematkan' }}</button>
                </form>
            </div>
            <x-hapus :action="route('pengumuman.destroy', $pengumuman)" />
        @endif
    </article>
</x-mading-layout>
