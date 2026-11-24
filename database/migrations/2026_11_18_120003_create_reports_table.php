<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('reports')) {
            Schema::create('reports', function (Blueprint $table) {
                $table->id();
                $table->string('report_number')->unique();
                $table->foreignId('branch_id')->constrained('branches')->cascadeOnDelete();
                $table->foreignId('asset_id')->constrained('assets')->restrictOnDelete();
                $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->string('reporter_name');
                $table->text('issue_description');
                $table->enum('status', ['menunggu', 'diproses', 'selesai', 'dibatalkan'])
                    ->default('menunggu')
                    ->index();
                $table->foreignId('technician_id')->nullable()->constrained('users')->nullOnDelete();
                $table->text('repair_notes')->nullable();
                $table->string('replaced_parts')->nullable();
                $table->decimal('repair_cost', 12, 2)->nullable();
                $table->dateTime('reported_at');
                $table->dateTime('processed_at')->nullable();
                $table->dateTime('resolved_at')->nullable();
                $table->timestamps();

                $table->index(['status', 'resolved_at']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
