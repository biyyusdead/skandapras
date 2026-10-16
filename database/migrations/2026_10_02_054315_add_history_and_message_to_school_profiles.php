<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('school_profiles', function (Blueprint $table) {
            $table->text('history')->nullable()->after('mission');
            $table->text('principal_message')->nullable()->after('history');
            $table->string('principal_photo')->nullable()->after('principal_message');
        });
    }

    public function down(): void
    {
        Schema::table('school_profiles', function (Blueprint $table) {
            $table->dropColumn(['history', 'principal_message', 'principal_photo']);
        });
    }
};
