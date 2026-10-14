<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hero_menus', function (Blueprint $table) {
            $table->id();
            $table->string('label');         // small label, mis. "Sarpras"
            $table->string('title');         // judul, mis. "Sarana & Prasarana"
            $table->string('description');   // desc singkat
            $table->string('icon');          // nama icon lucide, mis. "building-2"
            $table->string('route_name');    // nama route, mis. "public.rooms.index"
            $table->string('color_from');    // tailwind color from, mis. "emerald-500"
            $table->string('color_to');      // tailwind color to, mis. "emerald-700"
            $table->string('pattern')->default('dots');  // dots|grid|stripes|waves|cross
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