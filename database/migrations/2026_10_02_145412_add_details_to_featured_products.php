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
        Schema::table('featured_products', function (Blueprint $table) {
            $table->string('category', 50)->nullable()->after('description');
            $table->string('code', 20)->nullable()->after('category');
            $table->string('contact', 30)->nullable()->after('code');
        });
    }

    public function down(): void
    {
        Schema::table('featured_products', function (Blueprint $table) {
            $table->dropColumn(['category', 'code', 'contact']);
        });
    }
};
