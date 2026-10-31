<?php

namespace Tests\Feature;

use App\Models\Laporan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class LaporBanjirTest extends TestCase
{
    use RefreshDatabase;

    private function dataLaporan(array $override = []): array
    {
        return array_merge([
            'nama_pelapor' => 'Andi',
            'no_hp'        => '08123456789',
            'lokasi'       => 'Jl. Raya Baleendah',
            'kecamatan'    => 'Baleendah',
            'tinggi_air'   => 45,
            'keterangan'   => 'Air masuk rumah',
            'foto'         => UploadedFile::fake()->image('banjir.jpg'),
        ], $override);
    }

    private function buatLaporan(): Laporan
    {
        Storage::disk('public')->put('laporan/lama.jpg', 'isi');

        return Laporan::create([
            'nama_pelapor' => 'Budi', 'no_hp' => '08111111111', 'lokasi' => 'Dayeuhkolot',
            'kecamatan' => 'Dayeuhkolot', 'tinggi_air' => 80, 'foto' => 'laporan/lama.jpg',
        ]);
    }

    public function test_root_redirects_to_form(): void
    {
        $this->get('/')->assertRedirect(route('laporan.create'));
    }

    public function test_form_warga_tampil_tanpa_login(): void
    {
        $this->get(route('laporan.create'))
            ->assertOk()
            ->assertSee('Kirim Laporan')
            ->assertSee('multipart/form-data', false);
    }

    public function test_warga_bisa_kirim_laporan_dengan_foto(): void
    {
        Storage::fake('public');

        $this->post(route('laporan.store'), $this->dataLaporan())
            ->assertRedirect(route('laporan.create'))
            ->assertSessionHas('success');

        $laporan = Laporan::firstOrFail();
        $this->assertSame('Menunggu', $laporan->fresh()->status);
        Storage::disk('public')->assertExists($laporan->foto);
    }

    public function test_validasi_form_kosong(): void
    {
        $this->post(route('laporan.store'), [])
            ->assertSessionHasErrors(['nama_pelapor', 'no_hp', 'lokasi', 'kecamatan', 'tinggi_air', 'foto']);
    }

    public function test_foto_harus_gambar_dan_maks_2mb(): void
    {
        Storage::fake('public');

        $this->post(route('laporan.store'), $this->dataLaporan(['foto' => UploadedFile::fake()->create('a.pdf', 100, 'application/pdf')]))
            ->assertSessionHasErrors('foto');

        $this->post(route('laporan.store'), $this->dataLaporan(['foto' => UploadedFile::fake()->image('besar.jpg')->size(3000)]))
            ->assertSessionHasErrors('foto');

        $this->assertDatabaseCount('laporans', 0);
    }

    public function test_halaman_petugas_wajib_login(): void
    {
        $laporan = $this->buatLaporan();

        $this->get(route('laporan.index'))->assertRedirect(route('login'));
        $this->get(route('laporan.show', $laporan))->assertRedirect(route('login'));
        $this->get(route('laporan.edit', $laporan))->assertRedirect(route('login'));
        $this->put(route('laporan.update', $laporan), [])->assertRedirect(route('login'));
        $this->delete(route('laporan.destroy', $laporan))->assertRedirect(route('login'));
    }

    public function test_petugas_melihat_daftar_dan_detail(): void
    {
        Storage::fake('public');
        $laporan = $this->buatLaporan();
        $user = User::factory()->create();

        $this->actingAs($user)->get(route('laporan.index'))
            ->assertOk()->assertSee('Budi')->assertSee('Awas')->assertSee('Menunggu');

        $this->actingAs($user)->get(route('laporan.show', $laporan))
            ->assertOk()->assertSee('Dayeuhkolot')->assertSee('80 cm');
    }

    public function test_daftar_memakai_pagination_5_data(): void
    {
        Storage::fake('public');
        foreach (range(1, 7) as $i) {
            Laporan::create([
                'nama_pelapor' => "Pelapor $i", 'no_hp' => '08111111111', 'lokasi' => 'X',
                'kecamatan' => 'Y', 'tinggi_air' => 10, 'foto' => 'laporan/x.jpg',
            ]);
        }

        $this->actingAs(User::factory()->create())->get(route('laporan.index'))
            ->assertOk()->assertSee('Berikutnya');
    }

    public function test_petugas_ubah_status_dan_ganti_foto_menghapus_foto_lama(): void
    {
        Storage::fake('public');
        $laporan = $this->buatLaporan();

        $this->actingAs(User::factory()->create())
            ->put(route('laporan.update', $laporan), $this->dataLaporan([
                'status' => 'Diproses',
                'foto'   => UploadedFile::fake()->image('baru.png'),
            ]))
            ->assertRedirect(route('laporan.index'))
            ->assertSessionHas('success');

        $laporan->refresh();
        $this->assertSame('Diproses', $laporan->status);
        Storage::disk('public')->assertMissing('laporan/lama.jpg');
        Storage::disk('public')->assertExists($laporan->foto);
    }

    public function test_update_tanpa_foto_mempertahankan_foto_lama(): void
    {
        Storage::fake('public');
        $laporan = $this->buatLaporan();

        $this->actingAs(User::factory()->create())
            ->put(route('laporan.update', $laporan), $this->dataLaporan(['status' => 'Selesai', 'foto' => null]))
            ->assertSessionHasNoErrors();

        $this->assertSame('laporan/lama.jpg', $laporan->fresh()->foto);
        Storage::disk('public')->assertExists('laporan/lama.jpg');
    }

    public function test_status_tidak_valid_ditolak(): void
    {
        Storage::fake('public');
        $laporan = $this->buatLaporan();

        $this->actingAs(User::factory()->create())
            ->put(route('laporan.update', $laporan), $this->dataLaporan(['status' => 'Aneh', 'foto' => null]))
            ->assertSessionHasErrors('status');
    }

    public function test_hapus_laporan_ikut_menghapus_foto(): void
    {
        Storage::fake('public');
        $laporan = $this->buatLaporan();

        $this->actingAs(User::factory()->create())
            ->delete(route('laporan.destroy', $laporan))
            ->assertRedirect(route('laporan.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseCount('laporans', 0);
        Storage::disk('public')->assertMissing('laporan/lama.jpg');
    }
}
