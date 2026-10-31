@extends('layouts.app')

@section('title', 'Detail Laporan')

@section('content')

<h2>Detail Laporan</h2>

<div class="card">
    <img class="foto-detail" src="{{ asset('storage/' . $laporan->foto) }}" alt="Foto banjir">

    <p><strong>Nama Pelapor:</strong> {{ $laporan->nama_pelapor }}</p>
    <p><strong>No. HP:</strong> {{ $laporan->no_hp }}</p>
    <p><strong>Lokasi:</strong> {{ $laporan->lokasi }}</p>
    <p><strong>Kecamatan:</strong> {{ $laporan->kecamatan }}</p>
    <p>
        <strong>Tinggi Genangan:</strong> {{ $laporan->tinggi_air }} cm
        <span class="badge badge-{{ strtolower($laporan->tingkat) }}">{{ $laporan->tingkat }}</span>
    </p>
    <p><strong>Keterangan:</strong> {{ $laporan->keterangan ?? '-' }}</p>
    <p>
        <strong>Status:</strong>
        <span class="badge badge-{{ strtolower($laporan->status) }}">{{ $laporan->status }}</span>
    </p>
    <p><strong>Dikirim:</strong> {{ $laporan->created_at->format('d-m-Y H:i') }}</p>
</div>

<a href="{{ route('laporan.edit', $laporan) }}">Edit</a>
&nbsp;|&nbsp;
<a href="{{ route('laporan.index') }}">&larr; Kembali ke Daftar</a>

@endsection
