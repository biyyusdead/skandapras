<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            $table->decimal('map_x', 5, 2)->nullable()->after('panorama_file');
            $table->decimal('map_y', 5, 2)->nullable()->after('map_x');
            $table->string('map_floor', 10)->nullable()->after('map_y');
        });
    }

    public function down(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            $table->dropColumn(['map_x', 'map_y', 'map_floor']);
        });
    }
};