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
        Schema::table('departments', function (Blueprint $table) {
            $table->string('slug')->nullable()->unique()->after('code');
            $table->string('icon')->nullable()->after('image');
            $table->string('color', 20)->nullable()->after('icon');
            $table->text('vision')->nullable()->after('description');
            $table->text('mission')->nullable()->after('vision');
            $table->json('gallery')->nullable()->after('mission');
        });
    }

    public function down(): void
    {
        Schema::table('departments', function (Blueprint $table) {
            $table->dropColumn(['slug', 'icon', 'color', 'vision', 'mission', 'gallery']);
        });
    }
};
