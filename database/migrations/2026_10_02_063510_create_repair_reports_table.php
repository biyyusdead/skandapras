<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('repair_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('damage_report_id')->constrained('damage_reports')->cascadeOnDelete();

            // Teknisi bukan user sistem
            $table->string('technician_name');
            $table->string('technician_contact')->nullable();

            // Detail perbaikan
            $table->text('repair_description')->nullable();
            $table->decimal('repair_cost', 15, 2)->nullable();
            $table->date('started_at')->nullable();
            $table->date('completed_at')->nullable();

            // Bukti before/after dipisah
            $table->string('repair_evidence_before')->nullable();
            $table->string('repair_evidence_after')->nullable();

            // Status bahasa Inggris sesuai requirement tambahan
            $table->enum('status', ['ready', 'in_progress', 'completed'])
                ->default('ready');

            $table->text('notes')->nullable();

            // User yang mencatat (Waka Sarpras) — untuk audit
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();

            $table->index('status');
            $table->index('damage_report_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('repair_reports');
    }
};
