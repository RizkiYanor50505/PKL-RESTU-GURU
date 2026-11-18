<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('adv_pembayaran', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kontrak_sewa_id')
                ->constrained('adv_kontrak_sewa')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->cascadeOnUpdate()
                ->nullOnDelete();

            $table->date('tgl_bayar');
            $table->decimal('jumlah_bayar', 15, 2);
            $table->enum('jenis_pembayaran', ['bayar_awal', 'dp', 'pelunasan', 'tagihan_perusahaan']);
            $table->string('metode_pembayaran', 50)->nullable();
            $table->string('bukti_pembayaran')->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('adv_pembayaran');
    }
};
