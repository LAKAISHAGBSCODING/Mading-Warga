<x-mading-layout :title="$item->exists ? 'Edit pengumuman' : 'Buat pengumuman'">
    <form method="POST" enctype="multipart/form-data"
          action="{{ $item->exists ? route('pengumuman.update', $item) : route('pengumuman.store') }}"
          class="card space-y-5">
        @csrf
        @if($item->exists) @method('PUT') @endif

        <div>
            <label class="lbl">Judul</label>
            <input class="field" name="judul" value="{{ old('judul', $item->judul) }}" required>
        </div>
        <div>
            <label class="lbl">Isi pengumuman</label>
            <textarea class="field" name="isi" rows="8" required>{{ old('isi', $item->isi) }}</textarea>
        </div>
        <div>
            <label class="lbl">Lampiran <span class="font-normal text-slate-500">(foto atau PDF, boleh kosong)</span></label>
            <input type="file" name="lampiran" accept="image/*,.pdf" class="field">
        </div>

        <button class="btn-primary w-full"><x-ic name="save" class="h-5 w-5" /> Simpan</button>
        <a href="{{ route('pengumuman.index') }}" class="btn-outline w-full">Batal</a>
    </form>
</x-mading-layout>