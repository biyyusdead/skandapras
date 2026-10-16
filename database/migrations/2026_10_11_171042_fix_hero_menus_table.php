<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Drop dulu karena table kosong / salah struktur
        Schema::dropIfExists('hero_menus');

        // Recreate dengan struktur benar
        Schema::create('hero_menus', function (Blueprint $table) {
            $table->id();
            $table->string('label');
            $table->string('title');
            $table->string('description');
            $table->string('icon');
            $table->string('route_name');
            $table->string('color_from');
            $table->string('color_to');
            $table->string('pattern')->default('dots');
            $table->integer('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hero_menus');
    }
};
