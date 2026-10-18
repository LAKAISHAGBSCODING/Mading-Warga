<x-mading-layout title="Notifikasi" subtitle="Pemberitahuan terbaru untuk akunmu.">
    @if($jumlahBelumDibaca > 0)
        <form method="POST" action="{{ route('notifikasi.baca-semua') }}">
            @csrf
            @method('PATCH')
            <button type="submit" class="btn-outline w-full">Tandai semua sudah dibaca</button>
        </form>
    @endif

    @forelse($notifications as $notification)
        <article class="card space-y-3 {{ $notification->read_at ? '' : 'border-blue-200 bg-blue-50/40' }}">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <h2 class="text-lg font-extrabold leading-snug text-slate-900">{{ $notification->data['judul'] }}</h2>
                    <p class="mt-1 text-slate-600">{{ $notification->data['pesan'] }}</p>
                </div>
                @unless($notification->read_at)
                    <span class="shrink-0 rounded-full bg-blue-100 px-3 py-1 text-xs font-bold text-blue-800">Baru</span>
                @endunless
            </div>

            <div class="flex items-center justify-between gap-3 border-t border-slate-100 pt-3">
                <time class="meta" datetime="{{ $notification->created_at->toIso8601String() }}">
                    {{ $notification->created_at->translatedFormat('j F Y, H:i') }}
                </time>
                <form method="POST" action="{{ route('notifikasi.baca', $notification) }}">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="font-bold text-blue-700 hover:text-blue-900">
                        {{ $notification->read_at ? 'Buka' : 'Baca' }}
                    </button>
                </form>
            </div>
        </article>
    @empty
        <x-kosong text="Belum ada notifikasi." />
    @endforelse

    {{ $notifications->links() }}
</x-mading-layout>
