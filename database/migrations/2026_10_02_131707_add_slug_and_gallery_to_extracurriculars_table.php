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
        Schema::table('extracurriculars', function (Blueprint $table) {
            $table->string('slug')->nullable()->unique()->after('name');
            $table->string('coach')->nullable()->after('description');
            $table->string('schedule')->nullable()->after('coach');
            $table->text('vision')->nullable()->after('schedule');
            $table->text('mission')->nullable()->after('vision');
            $table->json('gallery')->nullable()->after('image');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('extracurriculars', function (Blueprint $table) {
            //
        });
    }
};
