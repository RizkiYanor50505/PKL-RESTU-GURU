<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('assets')) {
            Schema::create('assets', function (Blueprint $table) {
                $table->id();
                $table->foreignId('branch_id')->constrained('branches')->cascadeOnDelete();
                $table->string('code')->unique();
                $table->string('name');
                $table->string('subtitle')->nullable();
                $table->string('location')->index();
                $table->string('brand')->nullable();
                $table->enum('status', ['normal', 'dalam_perbaikan', 'dalam_kendala'])
                    ->default('normal')
                    ->index();
                $table->text('description')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('assets');
    }
};
