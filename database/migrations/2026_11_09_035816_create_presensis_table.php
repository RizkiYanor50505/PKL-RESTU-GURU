<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('presensis', function (Blueprint $table) {
            $table->id();
            $table->string('nik', 50);
            $table->date('attendance_date');
            $table->string('status', 20)->default('tepat_waktu');
            $table->unsignedSmallInteger('late_minutes')->default(0);

            $table->timestamp('masuk_at')->nullable();
            $table->decimal('masuk_latitude', 10, 7)->nullable();
            $table->decimal('masuk_longitude', 10, 7)->nullable();
            $table->decimal('masuk_accuracy', 8, 1)->nullable();
            $table->decimal('masuk_distance', 8, 1)->nullable();
            $table->string('masuk_ip', 45)->nullable();

            $table->timestamp('pulang_at')->nullable();
            $table->decimal('pulang_latitude', 10, 7)->nullable();
            $table->decimal('pulang_longitude', 10, 7)->nullable();
            $table->decimal('pulang_accuracy', 8, 1)->nullable();
            $table->decimal('pulang_distance', 8, 1)->nullable();
            $table->string('pulang_ip', 45)->nullable();

            $table->timestamps();

            $table->unique(['nik', 'attendance_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('presensis');
    }
};
