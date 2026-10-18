<x-mading-layout title="Agenda warga" subtitle="Kegiatan RT dan usulan dari warga.">
    <a href="{{ route('acara.create') }}" class="btn-primary w-full">
        <x-ic name="plus" class="h-5 w-5" />
        {{ auth()->user()->isAdmin() ? 'Buat acara' : 'Usulkan acara' }}
    </a>

    <form method="GET" action="{{ route('acara.index') }}" class="relative">
        <x-ic name="search" class="pointer-events-none absolute left-4 top-1/2 h-5 w-5 -translate-y-1/2 text-slate-400" />
        <input type="search" name="q" value="{{ $q }}" placeholder="Cari nama, lokasi, atau keterangan acara..."
               class="field !pl-12 !pr-28 [&::-webkit-search-cancel-button]:hidden" enterkeyhint="search">
        @if($q !== '')
            <a href="{{ route('acara.index') }}" aria-label="Hapus pencarian"
               class="absolute right-20 top-1/2 grid h-9 w-9 -translate-y-1/2 place-items-center rounded-full text-slate-400 hover:bg-slate-100 hover:text-slate-700">
                <x-ic name="x" class="h-5 w-5" />
            </a>
        @endif
        <button class="absolute right-2 top-1/2 -translate-y-1/2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">Cari</button>
    </form>

    @if($menunggu->count())
        <section class="space-y-3">
            <h2 class="text-lg font-bold text-slate-900">Usulan menunggu persetujuan</h2>
            @foreach($menunggu as $a)
                <div class="card space-y-3 border-amber-200 bg-amber-50/40">
                    <div>
                        <span class="inline-flex rounded-full bg-amber-100 text-amber-800 text-sm font-semibold px-3 py-1">Perlu ditinjau</span>
                        <p class="mt-2 font-bold text-slate-900 text-lg">{{ $a->judul }}</p>
                        <p class="meta">Diusulkan oleh {{ $a->pengaju->name }}</p>
                    </div>
                    <p class="meta flex items-center gap-1.5"><x-ic name="calendar" class="h-4 w-4" /> {{ $a->tanggal->translatedFormat('j F Y') }}, {{ substr($a->jam, 0, 5) }} WIB</p>
                    <p class="meta flex items-center gap-1.5"><x-ic name="pin" class="h-4 w-4" /> {{ $a->lokasi }}</p>
                    <a href="{{ route('acara.edit', $a) }}" class="btn-primary w-full">Tinjau dan putuskan</a>
                </div>
            @endforeach
        </section>
    @endif

    @if($usulanSaya->count())
        <section class="space-y-3">
            <h2 class="text-lg font-bold text-slate-900">Usulan saya</h2>
            @foreach($usulanSaya as $a)
                <div class="card space-y-3">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <p class="font-bold text-slate-900 text-lg">{{ $a->judul }}</p>
                            <p class="meta mt-0.5">{{ $a->tanggal->translatedFormat('j F Y') }}, {{ substr($a->jam, 0, 5) }} WIB &bull; {{ $a->lokasi }}</p>
                        </div>
                        @if($a->status === 'ditolak')
                            <span class="shrink-0 rounded-full bg-red-100 text-red-800 text-xs font-bold px-3 py-1">Ditolak</span>
                        @else
                            <span class="shrink-0 rounded-full bg-amber-100 text-amber-800 text-xs font-bold px-3 py-1">Menunggu</span>
                        @endif
                    </div>

                    @if($a->status === 'ditolak' && $a->alasan_penolakan)
                        <div class="rounded-xl border border-red-200 bg-red-50 p-3 text-sm text-red-800">
                            <span class="font-bold">Alasan Penolakan:</span> {{ $a->alasan_penolakan }}
                        </div>
                    @endif

                    @if($a->status === 'menunggu')
                        <div class="flex items-center gap-2 border-t border-slate-100 pt-3">
                            <a href="{{ route('acara.edit', $a) }}" class="btn-outline !min-h-[40px] !py-1.5 text-xs font-semibold">
                                <x-ic name="pencil" class="h-4 w-4" /> Ubah Usulan
                            </a>
                            <x-hapus :action="route('acara.destroy', $a)" judul="Batalkan usulan acara?" teks="Usulan acara ini akan dihapus." />
                        </div>
                    @endif
                </div>
            @endforeach
        </section>
    @endif

    <section class="space-y-3">
        <h2 class="text-lg font-bold text-slate-900">Acara mendatang</h2>
        @forelse($acaras as $a)
            @php
                $saya = $a->rsvps->firstWhere('user_id', auth()->id());
                $hadir = $a->rsvps->where('status', 'hadir');
                $tidak = $a->rsvps->where('status', 'tidak_hadir');
            @endphp
            <div id="acara-{{ $a->id }}" class="card space-y-4 scroll-mt-20">
                <div class="flex gap-4">
                    <div class="grid place-items-center h-16 w-16 shrink-0 rounded-xl bg-blue-600 text-white leading-none">
                        <div class="text-center">
                            <p class="text-2xl font-extrabold">{{ $a->tanggal->format('d') }}</p>
                            <p class="mt-1 text-xs font-bold uppercase tracking-wide">{{ $a->tanggal->translatedFormat('M') }}</p>
                        </div>
                    </div>
                    <div class="min-w-0">
                        <h3 class="text-xl font-extrabold leading-snug text-slate-900">{{ $a->judul }}</h3>
                        <p class="meta mt-1">{{ $a->tanggal->translatedFormat('l, j F Y') }}</p>
                    </div>
                </div>

                <div class="space-y-1.5 text-slate-700">
                    <p class="flex items-center gap-2"><x-ic name="clock" class="h-5 w-5 text-blue-600" /> {{ substr($a->jam, 0, 5) }} WIB</p>
                    <p class="flex items-center gap-2"><x-ic name="pin" class="h-5 w-5 text-blue-600" /> {{ $a->lokasi }}</p>
                </div>

                @if($a->deskripsi)
                    <p class="whitespace-pre-line text-slate-600 leading-relaxed">{{ $a->deskripsi }}</p>
                @endif

                <p class="chip"><x-ic name="users" class="h-4 w-4" /> {{ $hadir->count() }} warga akan hadir</p>
                <p class="text-sm text-slate-600">
                    {{ $a->rsvp_hadir_count }} hadir · {{ $a->rsvp_tidak_hadir_count }} tidak hadir
                    @if($a->kuota)
                        · Sisa kuota {{ max(0, $a->kuota - $a->rsvp_hadir_count) }} dari {{ $a->kuota }}
                    @else
                        · Tanpa batas kuota
                    @endif
                </p>

                <form method="POST" action="{{ route('acara.rsvp', $a) }}" class="grid grid-cols-2 gap-2">
                    @csrf
                    <button name="status" value="hadir"
                            @disabled($a->kuota !== null && $a->rsvp_hadir_count >= $a->kuota && $saya?->status !== 'hadir')
                            class="{{ $saya?->status === 'hadir' ? 'btn-primary' : 'btn-outline' }} disabled:cursor-not-allowed disabled:opacity-50">
                        <x-ic name="check" class="h-5 w-5" /> Hadir
                    </button>
                    <button name="status" value="tidak_hadir" class="{{ $saya?->status === 'tidak_hadir' ? 'btn-dark' : 'btn-outline' }}">
                        <x-ic name="x" class="h-5 w-5" /> Tidak hadir
                    </button>
                </form>

                @if(auth()->user()->isAdmin())
                <a href="{{ route('acara.rsvp.export', $a) }}" class="btn-outline w-full">
                    <x-ic name="doc" class="h-5 w-5" /> Unduh daftar RSVP (CSV)
                </a>
                <details class="rounded-xl bg-slate-50 p-4">
                        <summary class="cursor-pointer font-semibold text-slate-800">Rekap kehadiran</summary>
                        <p class="mt-3 font-semibold text-slate-900">Hadir ({{ $hadir->count() }})</p>
                        <ul class="mt-1 list-disc pl-5 text-slate-700">
                            @forelse($hadir as $r)<li>{{ $r->user->name }} ({{ $r->user->no_rumah }})</li>@empty<li>-</li>@endforelse
                        </ul>
                        <p class="mt-3 font-semibold text-slate-900">Tidak hadir ({{ $tidak->count() }})</p>
                        <ul class="mt-1 list-disc pl-5 text-slate-700">
                            @forelse($tidak as $r)<li>{{ $r->user->name }}</li>@empty<li>-</li>@endforelse
                        </ul>
                    </details>
                    <div class="grid grid-cols-2 gap-2 border-t border-slate-100 pt-4">
                        <a href="{{ route('acara.edit', $a) }}" class="btn-outline"><x-ic name="pencil" class="h-5 w-5" /> Edit</a>
                        <x-hapus :action="route('acara.destroy', $a)" />
                    </div>
                @endif
            </div>
        @empty
            <x-kosong text="Belum ada acara." />
        @endforelse
        {{ $acaras->links() }}
    </section>
</x-mading-layout>