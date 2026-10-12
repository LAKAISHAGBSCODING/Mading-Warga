<x-mading-layout title="Buat Postingan">
    <form method="POST" action="{{ route('forum.store') }}" class="card space-y-4">
        @csrf
        <div>
            <label class="lbl">Label</label>
            <select name="label" class="field">
                @foreach($labels as $l)
                    <option value="{{ $l }}" @selected(old('label') === $l)>{{ $l }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="lbl">Judul</label>
            <input class="field" name="judul" value="{{ old('judul') }}" required>
        </div>
        <div>
            <label class="lbl">Isi</label>
            <textarea class="field" name="isi" rows="6" required>{{ old('isi') }}</textarea>
        </div>
        <p class="text-sm text-gray-500">Mau jualan? Gunakan menu Lapak ya, supaya forum tetap rapi.</p>
        <button class="btn-primary w-full">📨 Kirim</button>
    </form>
</x-mading-layout>