@extends('errors.layout')

@section('code', '419')
@section('title', 'Sesi Halaman Berakhir')
@section('badge-class', 'bg-amber-50 text-amber-600')
@section('pill-class', 'bg-amber-100 text-amber-800')

@section('icon')
<svg class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
</svg>
@endsection

@section('message', 'Sesi halaman telah kedaluwarsa demi keamanan. Silakan muat ulang halaman atau coba kirim formulir kembali.')
