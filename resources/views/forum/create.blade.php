@php
    $isEdit = isset($post) && $post->exists;
@endphp
<x-mading-layout :title="$isEdit ? 'Ubah postingan' : 'Buat postingan'">
    <form method="POST" action="{{ $isEdit ? route('forum.update', $post) : route('forum.store') }}" class="card space-y-5">
        @csrf
        @if($isEdit)
            @method('PUT')
        @endif
        <div>
            <label class="lbl">Label</label>
            <select name="label" class="field">
                @foreach($labels as $l)
                    <option value="{{ $l }}" @selected(old('label', $post->label ?? '') === $l)>{{ $l }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="lbl">Judul</label>
            <input class="field" name="judul" value="{{ old('judul', $post->judul ?? '') }}" required>
        </div>
        <div>
            <label class="lbl">Isi</label>
            <textarea class="field" name="isi" rows="6" required>{{ old('isi', $post->isi ?? '') }}</textarea>
        </div>
        <p class="rounded-xl bg-blue-50 p-3 text-sm text-blue-800">Mau berjualan? Gunakan menu Lapak ya, supaya forum tetap rapi.</p>
        <button class="btn-primary w-full"><x-ic name="{{ $isEdit ? 'save' : 'send' }}" class="h-5 w-5" /> {{ $isEdit ? 'Simpan Perubahan' : 'Kirim Postingan' }}</button>
        <a href="{{ $isEdit ? route('forum.show', $post) : route('forum.index') }}" class="btn-outline w-full">Batal</a>
    </form>
</x-mading-layout>