<x-mading-layout>

    <section class="relative overflow-hidden rounded-3xl bg-blue-600 p-6 text-white">
        <div class="absolute -right-10 -top-10 h-40 w-40 rounded-full bg-white/10"></div>
        <div class="absolute -right-2 top-16 h-24 w-24 rounded-full bg-white/10"></div>
        <p class="relative text-blue-100 text-sm font-medium">{{ now()->translatedFormat('l, j F Y') }}</p>
        <h1 class="relative mt-1 text-[28px] leading-tight font-extrabold tracking-tight">
            Halo, {{ Str::words(auth()->user()->name, 1, '') }}
        </h1>
        <p class="relative mt-1 text-blue-100">Selamat datang di {{ config('app.name') }}.</p>
        @if(auth()->user()->no_rumah && auth()->user()->no_rumah !== '-')
            <span class="relative mt-4 inline-flex items-center gap-1.5 rounded-full bg-white/15 px-3 py-1 text-sm font-semibold">
                <x-ic name="home" class="h-4 w-4" /> {{ auth()->user()->no_rumah }}
            </span>
        @endif
    </section>

    @if(auth()->user()->isAdmin())
        <a href="{{ route('warga.index') }}" class="card flex items-center gap-4 hover:border-blue-300 transition">
            <span class="grid place-items-center h-12 w-12 rounded-xl bg-blue-50 text-blue-700">
                <x-ic name="users" class="h-6 w-6" />
            </span>
            <div class="flex-1">
                <p class="font-bold text-slate-900">Verifikasi warga</p>
                <p class="meta">
                    @if($jumlahPending > 0)
                        {{ $jumlahPending }} akun menunggu persetujuan
                    @else
                        Tidak ada akun yang menunggu
                    @endif
                </p>
            </div>
            @if($jumlahPending > 0)
                <span class="grid place-items-center min-w-7 h-7 rounded-full bg-blue-600 text-white text-sm font-bold px-2">{{ $jumlahPending }}</span>
            @endif
            <x-ic name="next" class="h-5 w-5 text-slate-400" />
        </a>
    @endif

    <section class="space-y-3">
        <div class="flex items-center justify-between">
            <h2 class="text-lg font-bold text-slate-900">Pengumuman terbaru</h2>
            <a href="{{ route('pengumuman.index') }}" class="text-sm font-semibold text-blue-700">Lihat semua</a>
        </div>
        @forelse($pengumumans as $p)
            <a href="{{ route('pengumuman.index') }}" class="card block hover:border-blue-300 transition">
                <p class="meta">{{ $p->created_at->translatedFormat('j F Y') }}</p>
                <p class="mt-1 font-bold text-slate-900">{{ $p->judul }}</p>
                <p class="mt-1 text-slate-600">{{ Str::limit($p->isi, 90) }}</p>
            </a>
        @empty
            <x-kosong text="Belum ada pengumuman." />
        @endforelse
    </section>

    <section class="space-y-3">
        <div class="flex items-center justify-between">
            <h2 class="text-lg font-bold text-slate-900">Acara mendatang</h2>
            <a href="{{ route('acara.index') }}" class="text-sm font-semibold text-blue-700">Lihat semua</a>
        </div>
        @forelse($acaras as $a)
            <a href="{{ route('acara.index') }}" class="card flex gap-4 hover:border-blue-300 transition">
                <div class="grid place-items-center h-16 w-16 shrink-0 rounded-xl bg-blue-50 text-blue-700 leading-none">
                    <div class="text-center">
                        <p class="text-2xl font-extrabold">{{ $a->tanggal->format('d') }}</p>
                        <p class="mt-1 text-xs font-bold uppercase tracking-wide">{{ $a->tanggal->translatedFormat('M') }}</p>
                    </div>
                </div>
                <div class="min-w-0">
                    <p class="font-bold text-slate-900">{{ $a->judul }}</p>
                    <p class="meta mt-1 flex items-center gap-1.5"><x-ic name="clock" class="h-4 w-4" /> {{ substr($a->jam, 0, 5) }} WIB</p>
                    <p class="meta flex items-center gap-1.5"><x-ic name="pin" class="h-4 w-4" /> {{ $a->lokasi }}</p>
                </div>
            </a>
        @empty
            <x-kosong text="Belum ada acara." />
        @endforelse
    </section>
</x-mading-layout>