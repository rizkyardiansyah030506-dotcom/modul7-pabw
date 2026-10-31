@extends('layouts.app')

@section('title', 'Daftar Laporan Banjir')

@section('content')

<h2>Daftar Laporan Banjir</h2>

@if (session('success'))
    <x-alert>{{ session('success') }}</x-alert>
@endif

<table>
    <thead>
        <tr>
            <th>No</th>
            <th>Foto</th>
            <th>Pelapor</th>
            <th>Lokasi</th>
            <th>Tinggi Air</th>
            <th>Status</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($laporans as $laporan)
            <tr>
                <td>{{ $laporans->firstItem() + $loop->index }}</td>
                <td>
                    <img class="thumb" src="{{ asset('storage/' . $laporan->foto) }}" alt="Foto banjir">
                </td>
                <td>
                    {{ $laporan->nama_pelapor }}<br>
                    <small>{{ $laporan->no_hp }}</small>
                </td>
                <td>
                    {{ $laporan->lokasi }}<br>
                    <small>Kec. {{ $laporan->kecamatan }}</small>
                </td>
                <td>
                    {{ $laporan->tinggi_air }} cm<br>
                    <span class="badge badge-{{ strtolower($laporan->tingkat) }}">{{ $laporan->tingkat }}</span>
                </td>
                <td>
                    <span class="badge badge-{{ strtolower($laporan->status) }}">{{ $laporan->status }}</span>
                </td>
                <td class="aksi">
                    <a href="{{ route('laporan.show', $laporan) }}">Detail</a> |
                    <a href="{{ route('laporan.edit', $laporan) }}">Edit</a> |
                    <form action="{{ route('laporan.destroy', $laporan) }}" method="POST"
                          onsubmit="return confirm('Hapus laporan ini beserta fotonya?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn-link">Hapus</button>
                    </form>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="7" style="text-align:center">Belum ada laporan banjir.</td>
            </tr>
        @endforelse
    </tbody>
</table>

{{ $laporans->links() }}

@endsection
