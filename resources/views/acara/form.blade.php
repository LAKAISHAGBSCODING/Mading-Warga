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

        @if(auth()->user()->isAdmin() && $item->exists)
            <div>
                <label class="lbl">Status Persetujuan RT</label>
                <select class="field" name="status" id="status-acara" onchange="toggleAlasan(this.value)">
                    @foreach(['menunggu' => 'Menunggu persetujuan', 'disetujui' => 'Disetujui (publikasikan ke warga)', 'ditolak' => 'Ditolak'] as $v => $l)
                        <option value="{{ $v }}" @selected(old('status', $item->status) === $v)>{{ $l }}</option>
                    @endforeach
                </select>
            </div>
            <div id="wrapper-alasan" class="{{ old('status', $item->status) === 'ditolak' ? '' : 'hidden' }}">
                <label class="lbl text-red-700">Alasan Penolakan <span class="font-normal text-slate-500">(disampaikan kepada pengusul)</span></label>
                <textarea class="field border-red-300 focus:border-red-500" name="alasan_penolakan" rows="3" placeholder="Contoh: Jadwal bentrok dengan kegiatan kerja bakti RT...">{{ old('alasan_penolakan', $item->alasan_penolakan) }}</textarea>
            </div>
            <script>
                function toggleAlasan(val) {
                    const el = document.getElementById('wrapper-alasan');
                    if (val === 'ditolak') {
                        el.classList.remove('hidden');
                    } else {
                        el.classList.add('hidden');
                    }
                }
            </script>
        @endif

        <button class="btn-primary w-full"><x-ic name="save" class="h-5 w-5" /> Simpan</button>
        <a href="{{ route('acara.index') }}" class="btn-outline w-full">Batal</a>
    </form>
</x-mading-layout>