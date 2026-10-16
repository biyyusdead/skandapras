<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('alumni', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('photo')->nullable();
            $table->year('graduation_year')->nullable();
            $table->string('department')->nullable();
            $table->string('achievement')->nullable();   // Prestasi saat jadi siswa
            $table->string('motto')->nullable();         // Motto
            $table->text('description')->nullable();     // Deskripsi
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alumni');
    }
};
