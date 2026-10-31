@extends('layouts.app')

@section('title', 'Form Laporan Banjir')

@section('content')

<h2>Form Pelaporan Banjir</h2>

@if (session('success'))
    <x-alert>{{ session('success') }}</x-alert>
@endif

{{-- enctype wajib agar file foto ikut terkirim; novalidate agar pesan galat Laravel yang tampil --}}
<form action="{{ route('laporan.store') }}" method="POST" enctype="multipart/form-data" novalidate>

    @csrf

    <label for="nama_pelapor">Nama Pelapor</label>
    <input type="text" id="nama_pelapor" name="nama_pelapor" value="{{ old('nama_pelapor') }}">
    @error('nama_pelapor') <div class="error">{{ $message }}</div> @enderror

    <label for="no_hp">No. HP</label>
    <input type="text" id="no_hp" name="no_hp" value="{{ old('no_hp') }}" placeholder="08123456789">
    @error('no_hp') <div class="error">{{ $message }}</div> @enderror

    <label for="lokasi">Lokasi Kejadian</label>
    <input type="text" id="lokasi" name="lokasi" value="{{ old('lokasi') }}" placeholder="Jl. ..., RT/RW, desa">
    @error('lokasi') <div class="error">{{ $message }}</div> @enderror

    <label for="kecamatan">Kecamatan</label>
    <input type="text" id="kecamatan" name="kecamatan" value="{{ old('kecamatan') }}" placeholder="Dayeuhkolot">
    @error('kecamatan') <div class="error">{{ $message }}</div> @enderror

    <label for="tinggi_air">Tinggi Genangan Air (cm)</label>
    <input type="number" id="tinggi_air" name="tinggi_air" value="{{ old('tinggi_air') }}" min="0">
    @error('tinggi_air') <div class="error">{{ $message }}</div> @enderror

    <label for="keterangan">Keterangan (opsional)</label>
    <textarea id="keterangan" name="keterangan" rows="3">{{ old('keterangan') }}</textarea>
    @error('keterangan') <div class="error">{{ $message }}</div> @enderror

    <label for="foto">Foto Bukti (jpg/jpeg/png, maks. 2 MB)</label>
    <input type="file" id="foto" name="foto" accept="image/png,image/jpeg">
    @error('foto') <div class="error">{{ $message }}</div> @enderror

    <button type="submit">Kirim Laporan</button>

</form>

@endsection
