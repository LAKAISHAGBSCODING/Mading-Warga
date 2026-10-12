<x-mading-layout :title="$item->exists ? 'Edit Pengumuman' : 'Buat Pengumuman'">
    <form method="POST" enctype="multipart/form-data"
          action="{{ $item->exists ? route('pengumuman.update', $item) : route('pengumuman.store') }}"
          class="card space-y-4">
        @csrf
        @if($item->exists) @method('PUT') @endif

        <div>
            <label class="lbl">Judul</label>
            <input class="field" name="judul" value="{{ old('judul', $item->judul) }}" required>
        </div>
        <div>
            <label class="lbl">Isi Pengumuman</label>
            <textarea class="field" name="isi" rows="7" required>{{ old('isi', $item->isi) }}</textarea>
        </div>
        <div>
            <label class="lbl">Lampiran (foto atau PDF, boleh kosong)</label>
            <input type="file" name="lampiran" accept="image/*,.pdf" class="field">
        </div>
        <button class="btn-primary w-full">💾 Simpan</button>
    </form>
</x-mading-layout>