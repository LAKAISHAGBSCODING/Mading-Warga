@extends('errors.layout')

@section('code', '404')
@section('title', 'Halaman Tidak Ditemukan')
@section('badge-class', 'bg-blue-50 text-blue-600')
@section('pill-class', 'bg-blue-100 text-blue-800')

@section('icon')
<svg class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
</svg>
@endsection

@section('message', 'Halaman yang Anda tuju mungkin sudah dihapus, dipindahkan, atau alamat URL yang dimasukkan kurang tepat.')
