<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('adv_kontrak_sewa', function (Blueprint $table) {
            $table->dropUnique(['no_penawaran']);
            $table->index('no_penawaran');
        });
    }

    public function down(): void
    {
        Schema::table('adv_kontrak_sewa', function (Blueprint $table) {
            $table->dropIndex(['no_penawaran']);
            $table->unique('no_penawaran');
        });
    }
};
