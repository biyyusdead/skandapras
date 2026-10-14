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
        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code', 30)->unique();
            $table->string('floor', 20)->nullable();
            $table->integer('capacity')->nullable();
            $table->text('description')->nullable();
            $table->enum('condition', ['baik', 'rusak_ringan', 'rusak_berat'])->default('baik');
            $table->unsignedTinyInteger('health_score')->default(100);
            $table->string('photo')->nullable();
            $table->string('panorama_url')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('condition');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rooms');
    }
};
