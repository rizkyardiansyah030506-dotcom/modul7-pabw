<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('laporans', function (Blueprint $table) {
            $table->id();
            $table->string('nama_pelapor');                  // Nama warga pelapor
            $table->string('no_hp', 20);                     // Nomor HP pelapor
            $table->string('lokasi');                        // Alamat / lokasi banjir
            $table->string('kecamatan');                     // Kecamatan
            $table->unsignedSmallInteger('tinggi_air');      // Tinggi air dalam cm
            $table->text('keterangan')->nullable();          // Boleh kosong
            $table->string('foto');                          // Path foto di storage
            $table->string('status')->default('Menunggu');   // Menunggu / Diproses / Selesai
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('laporans');
    }
};
