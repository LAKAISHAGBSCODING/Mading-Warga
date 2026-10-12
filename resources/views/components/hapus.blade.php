@props(['action'])
<form method="POST" action="{{ $action }}" onsubmit="return confirm('Yakin ingin menghapus?')">
    @csrf
    @method('DELETE')
    <button class="btn-danger w-full">🗑️ Hapus</button>
</form>