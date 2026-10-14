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
        Schema::table('teachers', function (Blueprint $table) {
            $table->enum('gender', ['L', 'P'])->nullable()->after('name');
            $table->string('subject')->nullable()->after('position');
            $table->string('competency')->nullable()->after('subject');
        });
    }

    public function down(): void
    {
        Schema::table('teachers', function (Blueprint $table) {
            $table->dropColumn(['gender', 'subject', 'competency']);
        });
    }
};
