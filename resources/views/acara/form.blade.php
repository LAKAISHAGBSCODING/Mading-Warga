<x-mading-layout :title="$item->exists ? 'Tinjau acara' : (auth()->user()->isAdmin() ? 'Buat acara' : 'Usulkan acara')">
    <form method="POST"
          action="{{ $item->exists ? route('acara.update', $item) : route('acara.store') }}"
          class="card space-y-5">
        @csrf
        @if($item->exists) @method('PUT') @endif

        <div>
            <label class="lbl">Nama acara</label>
            <input class="field" name="judul" value="{{ old('judul', $item->judul) }}" required>
        </div>
        <div class="grid grid-cols-2 gap-3">
            <div>
                <label class="lbl">Tanggal</label>
                <input type="date" class="field" name="tanggal"
                       value="{{ old('tanggal', $item->tanggal?->format('Y-m-d')) }}" required>
            </div>
            <div>
                <label class="lbl">Jam</label>
                <input type="time" class="field" name="jam"
                       value="{{ old('jam', $item->jam ? substr($item->jam, 0, 5) : '') }}" required>
            </div>
        </div>
        <div>
            <label class="lbl">Lokasi</label>
            <input class="field" name="lokasi" value="{{ old('lokasi', $item->lokasi) }}" required>
        </div>
        <div>
            <label class="lbl">Keterangan <span class="font-normal text-slate-500">(boleh kosong)</span></label>
            <textarea class="field" name="deskripsi" rows="4">{{ old('deskripsi', $item->deskripsi) }}</textarea>
        </div>

        @if($item->exists)
            <div>
                <label class="lbl">Status</label>
                <select class="field" name="status">
                    @foreach(['menunggu' => 'Menunggu persetujuan', 'disetujui' => 'Disetujui (tampil di kalender)', 'ditolak' => 'Ditolak'] as $v => $l)
                        <option value="{{ $v }}" @selected(old('status', $item->status) === $v)>{{ $l }}</option>
                    @endforeach
                </select>
            </div>
        @endif

        <button class="btn-primary w-full"><x-ic name="save" class="h-5 w-5" /> Simpan</button>
        <a href="{{ route('acara.index') }}" class="btn-outline w-full">Batal</a>
    </form>
</x-mading-layout>