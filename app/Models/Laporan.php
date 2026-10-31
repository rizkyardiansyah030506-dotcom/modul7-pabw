<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class Laporan extends Model
{
    protected $table = 'laporans';

    // Pilihan status penanganan oleh petugas
    public const STATUS = ['Menunggu', 'Diproses', 'Selesai'];

    protected $fillable = [
        'nama_pelapor',
        'no_hp',
        'lokasi',
        'kecamatan',
        'tinggi_air',
        'keterangan',
        'foto',
        'status',
    ];

    // Tingkat genangan (dari modul 5): < 30 cm Waspada, 30-70 cm Siaga, > 70 cm Awas
    protected function tingkat(): Attribute
    {
        return Attribute::get(fn () => match (true) {
            $this->tinggi_air < 30  => 'Waspada',
            $this->tinggi_air <= 70 => 'Siaga',
            default                 => 'Awas',
        });
    }
}
