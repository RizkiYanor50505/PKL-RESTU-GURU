<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('adv_kontrak_sewa', function (Blueprint $table) {
            $table->id();
            $table->foreignId('titik_reklame_id')
                ->constrained('adv_titik_reklame')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
            $table->foreignId('klien_id')
                ->constrained('adv_klien')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->cascadeOnUpdate()
                ->nullOnDelete();

            $table->string('no_penawaran', 60)->unique();
            $table->string('no_kontrak', 60)->nullable()->unique();
            $table->date('tgl_penawaran');
            $table->date('tgl_acc')->nullable();
            $table->date('tgl_mulai')->nullable();
            $table->date('tgl_berakhir')->nullable();
            $table->unsignedTinyInteger('durasi_bulan')->nullable();
            $table->decimal('nilai_kontrak', 15, 2)->default(0);

            $table->text('materi_iklan')->nullable();
            $table->date('tgl_pemasangan')->nullable();
            $table->string('file_berita_acara')->nullable();

            $table->enum('status_kontrak', ['penawaran', 'acc', 'aktif', 'selesai', 'batal'])
                ->default('penawaran');
            $table->enum('status_perpanjangan', ['belum_konfirmasi', 'diperpanjang', 'tidak_diperpanjang'])
                ->default('belum_konfirmasi');
            $table->enum('status_materi', ['belum_pasang', 'terpasang', 'perlu_dicopot', 'sudah_dicopot'])
                ->default('belum_pasang');

            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('adv_kontrak_sewa');
    }
};
