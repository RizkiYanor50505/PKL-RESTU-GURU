<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('adv_titik_reklame', function (Blueprint $table) {
            $table->id();
            $table->string('kode_titik', 30)->unique();
            $table->enum('kategori_space', ['internal', 'mitra'])->default('internal');
            $table->string('wilayah', 100);
            $table->text('alamat_lokasi');
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->string('google_maps_url')->nullable();
            $table->string('ukuran', 50);
            $table->string('spesifikasi_lampu', 100)->nullable();
            $table->string('foto_papan')->nullable();
            $table->enum('status_titik', ['Tersedia', 'Tersewa', 'Maintenance'])->default('Tersedia');
            $table->string('no_izin', 100)->nullable();
            $table->date('tgl_izin_berakhir')->nullable();
            $table->date('tgl_pajak_berakhir')->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('adv_titik_reklame');
    }
};
