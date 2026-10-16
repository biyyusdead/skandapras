<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('approvals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('damage_report_id')->constrained('damage_reports')->cascadeOnDelete();
            $table->foreignId('approver_id')->constrained('users')->cascadeOnDelete();
            $table->unsignedTinyInteger('approval_level'); // 1 = Waka, 2 = Kepsek

            // Status bahasa Inggris
            $table->enum('status', ['pending', 'approved', 'rejected'])
                ->default('pending');

            $table->text('notes')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();

            // Satu laporan hanya boleh punya 1 approval per level
            $table->unique(['damage_report_id', 'approval_level']);

            $table->index('status');
            $table->index('approver_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('approvals');
    }
};
