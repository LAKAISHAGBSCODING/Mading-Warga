@props(['action'])
<form method="POST" action="{{ $action }}" onsubmit="return confirm('Yakin ingin menghapus?')">
    @csrf
    @method('DELETE')
    <button class="btn-danger w-full">
        <x-ic name="trash" class="h-5 w-5" /> Hapus
    </button>
</form>