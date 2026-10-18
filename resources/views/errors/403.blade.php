@extends('errors.layout')

@section('code', '403')
@section('title', 'Akses Tidak Diizinkan')
@section('badge-class', 'bg-amber-50 text-amber-600')
@section('pill-class', 'bg-amber-100 text-amber-800')

@section('icon')
<svg class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
</svg>
@endsection

@section('message', 'Maaf, akun Anda tidak memiliki hak akses untuk membuka atau mengubah halaman ini.')
