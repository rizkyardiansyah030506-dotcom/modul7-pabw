<?php

namespace App\Http\Controllers;

use App\Models\Laporan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class LaporBanjirController extends Controller
{
    // ===== BAGIAN WARGA (tanpa login) =====

    // CREATE: form pelaporan
    public function create(): View
    {
        return view('laporan.create');
    }

    // CREATE: validasi + simpan laporan beserta foto
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate(
            $this->rules(fotoWajib: true),
            $this->messages(),
            $this->attributes()
        );

        // simpan ke storage/app/public/laporan, di database hanya path-nya
        $data['foto'] = $request->file('foto')->store('laporan', 'public');

        Laporan::create($data); // status otomatis "Menunggu" (nilai bawaan migration)

        return redirect()->route('laporan.create')
            ->with('success', 'Laporan banjir berhasil dikirim. Terima kasih, petugas BPBD akan segera menindaklanjuti.');
    }

    // ===== BAGIAN PETUGAS (di dalam middleware auth) =====

    // READ: daftar laporan + thumbnail + pagination
    public function index(): View
    {
        $laporans = Laporan::latest()->paginate(5);

        return view('laporan.index', compact('laporans'));
    }

    // READ: detail satu laporan
    public function show(Laporan $laporan): View
    {
        return view('laporan.show', compact('laporan'));
    }

    // UPDATE: form edit
    public function edit(Laporan $laporan): View
    {
        return view('laporan.edit', compact('laporan'));
    }

    // UPDATE: simpan perubahan (foto baru opsional, status bisa diubah)
    public function update(Request $request, Laporan $laporan): RedirectResponse
    {
        $data = $request->validate(
            $this->rules(fotoWajib: false) + [
                'status' => ['required', Rule::in(Laporan::STATUS)],
            ],
            $this->messages(),
            $this->attributes()
        );

        if ($request->hasFile('foto')) {
            // simpan foto baru dulu, baru hapus foto lama
            $fotoBaru = $request->file('foto')->store('laporan', 'public');
            Storage::disk('public')->delete($laporan->foto);
            $data['foto'] = $fotoBaru;
        } else {
            unset($data['foto']); // tidak ganti foto -> pakai foto lama
        }

        $laporan->update($data);

        return redirect()->route('laporan.index')
            ->with('success', 'Laporan berhasil diperbarui.');
    }

    // DELETE: hapus data + file foto
    public function destroy(Laporan $laporan): RedirectResponse
    {
        Storage::disk('public')->delete($laporan->foto); // hapus file fisik
        $laporan->delete();                              // hapus data di database

        return redirect()->route('laporan.index')
            ->with('success', 'Laporan berhasil dihapus.');
    }

    // ===== ATURAN VALIDASI =====

    private function rules(bool $fotoWajib): array
    {
        return [
            'nama_pelapor' => 'required|string|max:100',
            'no_hp'        => ['required', 'regex:/^[0-9+]{8,15}$/'],
            'lokasi'       => 'required|string|max:255',
            'kecamatan'    => 'required|string|max:100',
            'tinggi_air'   => 'required|integer|min:0|max:1000',
            'keterangan'   => 'nullable|string|max:1000',
            'foto'         => [$fotoWajib ? 'required' : 'nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'], // maks. 2 MB
        ];
    }

    private function messages(): array
    {
        return [
            'required'      => ':attribute wajib diisi.',
            'string'        => ':attribute harus berupa teks.',
            'integer'       => ':attribute harus berupa angka bulat.',
            'max'           => ':attribute tidak boleh lebih dari :max.',
            'min'           => ':attribute tidak boleh kurang dari :min.',
            'no_hp.regex'   => 'No. HP hanya boleh angka (boleh diawali +), 8-15 digit.',
            'foto.required' => 'Foto bukti wajib diunggah.',
            'foto.image'    => 'File harus berupa gambar.',
            'foto.mimes'    => 'Foto harus berformat jpg, jpeg, atau png.',
            'foto.max'      => 'Ukuran foto maksimal 2 MB.',
            'foto.uploaded' => 'Foto gagal diunggah. Pastikan ukuran maksimal 2 MB.',
            'status.in'     => 'Status tidak valid.',
        ];
    }

    private function attributes(): array
    {
        return [
            'nama_pelapor' => 'Nama pelapor',
            'no_hp'        => 'No. HP',
            'lokasi'       => 'Lokasi',
            'kecamatan'    => 'Kecamatan',
            'tinggi_air'   => 'Tinggi air',
            'keterangan'   => 'Keterangan',
            'foto'         => 'Foto',
            'status'       => 'Status',
        ];
    }
}
