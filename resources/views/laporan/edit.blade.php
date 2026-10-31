@extends('layouts.app')

@section('title', 'Edit Laporan')

@section('content')

<h2>Edit Laporan</h2>

<div class="card">
<form action="{{ route('laporan.update', $laporan) }}" method="POST" enctype="multipart/form-data" novalidate>

    @csrf
    @method('PUT')

    <label for="nama_pelapor">Nama Pelapor</label>
    <input type="text" id="nama_pelapor" name="nama_pelapor" value="{{ old('nama_pelapor', $laporan->nama_pelapor) }}">
    @error('nama_pelapor') <div class="error">{{ $message }}</div> @enderror

    <label for="no_hp">No. HP</label>
    <input type="text" id="no_hp" name="no_hp" value="{{ old('no_hp', $laporan->no_hp) }}">
    @error('no_hp') <div class="error">{{ $message }}</div> @enderror

    <label for="lokasi">Lokasi Kejadian</label>
    <input type="text" id="lokasi" name="lokasi" value="{{ old('lokasi', $laporan->lokasi) }}">
    @error('lokasi') <div class="error">{{ $message }}</div> @enderror

    <label for="kecamatan">Kecamatan</label>
    <input type="text" id="kecamatan" name="kecamatan" value="{{ old('kecamatan', $laporan->kecamatan) }}">
    @error('kecamatan') <div class="error">{{ $message }}</div> @enderror

    <label for="tinggi_air">Tinggi Genangan Air (cm)</label>
    <input type="number" id="tinggi_air" name="tinggi_air" min="0" value="{{ old('tinggi_air', $laporan->tinggi_air) }}">
    @error('tinggi_air') <div class="error">{{ $message }}</div> @enderror

    <label for="keterangan">Keterangan</label>
    <textarea id="keterangan" name="keterangan" rows="3">{{ old('keterangan', $laporan->keterangan) }}</textarea>
    @error('keterangan') <div class="error">{{ $message }}</div> @enderror

    <label for="status">Status Penanganan</label>
    <select id="status" name="status">
        @foreach (\App\Models\Laporan::STATUS as $status)
            <option value="{{ $status }}" @selected(old('status', $laporan->status) === $status)>{{ $status }}</option>
        @endforeach
    </select>
    @error('status') <div class="error">{{ $message }}</div> @enderror

    <label>Foto Saat Ini</label>
    <img class="foto-edit" src="{{ asset('storage/' . $laporan->foto) }}" alt="Foto banjir">

    <label for="foto">Ganti Foto (opsional, jpg/jpeg/png, maks. 2 MB)</label>
    <input type="file" id="foto" name="foto" accept="image/png,image/jpeg">
    @error('foto') <div class="error">{{ $message }}</div> @enderror

    <button type="submit">Simpan Perubahan</button>
    <a href="{{ route('laporan.index') }}" class="btn-abu">Batal</a>

</form>
</div>

@endsection
