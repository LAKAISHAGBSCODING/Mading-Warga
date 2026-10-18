@extends('errors.layout')

@section('code', '500')
@section('title', 'Terjadi Gangguan Sistem')
@section('badge-class', 'bg-red-50 text-red-600')
@section('pill-class', 'bg-red-100 text-red-800')

@section('icon')
<svg class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
</svg>
@endsection

@section('message', 'Terjadi kesalahan teknis pada sistem server kami. Tim pengelola telah mencatat kendala ini dan sedang memperbaikinya.')
