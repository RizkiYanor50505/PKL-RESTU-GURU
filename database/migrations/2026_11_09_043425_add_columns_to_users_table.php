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
        Schema::table('users', function (Blueprint $table) {
            $table->string('nik', 50)->nullable()->unique()->after('id');
            $table->string('role', 20)->default('karyawan')->after('password');
            $table->string('device_id', 100)->nullable()->after('role');
            $table->boolean('is_active')->default(true)->after('device_id');
            $table->string('division', 100)->nullable()->after('is_active');
            $table->foreignId('shift_id')->nullable()->after('division')->constrained('shifts')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('shift_id');
            $table->dropColumn(['nik', 'role', 'device_id', 'is_active', 'division']);
        });
    }
};
