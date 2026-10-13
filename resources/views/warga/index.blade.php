<x-mading-layout title="Verifikasi warga" subtitle="Periksa data lalu setujui akun warga.">
    <div class="grid grid-cols-3 gap-1 rounded-2xl bg-slate-200/70 p-1 text-center text-[15px]">
        @foreach(['pending' => 'Menunggu', 'disetujui' => 'Disetujui', 'ditolak' => 'Ditolak'] as $k => $label)
            <a href="{{ route('warga.index', ['status' => $k]) }}"
               class="rounded-xl py-2.5 font-semibold transition {{ $status === $k ? 'bg-white text-blue-700 shadow-sm' : 'text-slate-600' }}">
                {{ $label }}
            </a>
        @endforeach
    </div>

    @forelse($warga as $w)
        <div class="card space-y-4">
            <div class="flex items-center gap-4">
                <span class="grid place-items-center h-12 w-12 shrink-0 rounded-full bg-blue-100 text-blue-700 text-lg font-bold">
                    {{ Str::upper(Str::substr($w->name, 0, 1)) }}
                </span>
                <div class="min-w-0">
                    <p class="font-bold text-slate-900 text-lg leading-tight">{{ $w->name }}</p>
                    <p class="meta">Daftar {{ $w->created_at->translatedFormat('j F Y') }}</p>
                </div>
            </div>

            <dl class="grid grid-cols-2 gap-3 rounded-xl bg-slate-50 p-3 text-[15px]">
                <div>
                    <dt class="meta">Rumah / Blok</dt>
                    <dd class="font-semibold text-slate-900">{{ $w->no_rumah }}</dd>
                </div>
                <div>
                    <dt class="meta">WhatsApp</dt>
                    <dd class="font-semibold text-slate-900">{{ $w->no_wa }}</dd>
                </div>
            </dl>

            <a href="https://wa.me/{{ $w->no_wa }}" target="_blank" class="btn-wa w-full">
                <x-ic name="chat" class="h-5 w-5" /> Cek lewat WhatsApp
            </a>

            <form method="POST" action="{{ route('warga.update', $w) }}" class="grid grid-cols-2 gap-2">
                @csrf
                @method('PATCH')
                @if($w->status_verifikasi !== 'disetujui')
                    <button name="status" value="disetujui" class="btn-primary">
                        <x-ic name="check" class="h-5 w-5" /> Setujui
                    </button>
                @endif
                @if($w->status_verifikasi !== 'ditolak')
                    <button name="status" value="ditolak" class="btn-danger"
        data-konfirm="Warga ini tidak akan bisa masuk ke aplikasi."
        data-konfirm-judul="Tolak pendaftaran?"
        data-konfirm-ya="Ya, tolak">
    <x-ic name="x" class="h-5 w-5" /> Tolak
</button>
                @endif
            </form>
        </div>
    @empty
        <x-kosong text="Tidak ada data." />
    @endforelse
</x-mading-layout>