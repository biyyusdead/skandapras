<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('damage_reports', function (Blueprint $table) {
            $table->id();
            $table->string('report_code', 30)->unique();
            $table->foreignId('room_id')->constrained('rooms')->cascadeOnDelete();
            $table->foreignId('item_room_id')->nullable()->constrained('item_rooms')->nullOnDelete();
            $table->foreignId('reporter_id')->constrained('users')->cascadeOnDelete();
            $table->text('description');
            $table->string('evidence_photo')->nullable();
            $table->timestamp('reported_at')->nullable();
            $table->enum('status', [
                'menunggu_verifikasi',
                'diverifikasi',
                'menunggu_persetujuan_1',
                'menunggu_persetujuan_2',
                'disetujui',
                'siap_diperbaiki',
                'diproses',
                'selesai',
                'ditolak',
            ])->default('menunggu_verifikasi');
            $table->text('verification_note')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index('status');
            $table->index('reported_at');
            $table->index('reporter_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('damage_reports');
    }
};
