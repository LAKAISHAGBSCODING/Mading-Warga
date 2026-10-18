@extends('errors.layout')

@section('code', '503')
@section('title', 'Layanan Sedang Pemeliharaan')
@section('badge-class', 'bg-blue-50 text-blue-600')
@section('pill-class', 'bg-blue-100 text-blue-800')

@section('icon')
<svg class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
    <path stroke-linecap="round" stroke-linejoin="round" d="M11.42 15.17L17.25 21A2.652 2.652 0 0021 17.25l-5.877-5.877M11.42 15.17l2.496-3.03c.317-.384.74-.626 1.208-.766M11.42 15.17l-4.655 5.653a2.548 2.548 0 11-3.586-3.586l6.837-5.63m5.108-.233c.55-.164 1.163-.188 1.743-.07a4.5 4.5 0 004.486-6.32l-3.27 3.287a1.5 1.5 0 01-2.122-2.122l3.287-3.27a4.5 4.5 0 00-6.32 4.486c.118.58.094 1.193-.07 1.743" />
</svg>
@endsection

@section('message', 'Aplikasi Mading Warga sedang dalam peningkatan atau pemeliharaan sistem berkala. Silakan kembali beberapa saat lagi.')
