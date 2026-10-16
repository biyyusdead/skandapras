<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('school_profiles')) {
            Schema::create('school_profiles', function (Blueprint $table) {
                $table->id();
                $table->string('school_name')->default('');
                $table->string('npsn', 20)->nullable()->unique();
                $table->text('address')->nullable();
                $table->string('phone', 30)->nullable();
                $table->string('email')->nullable();
                $table->string('principal_name')->nullable();
                $table->string('accreditation', 10)->nullable();
                $table->string('education_level', 20)->nullable();
                $table->text('description')->nullable();
                $table->string('logo')->nullable();
                $table->timestamps();
            });
        }

        Schema::table('school_profiles', function (Blueprint $table) {
            $table->text('vision')->nullable()->after('description');
            $table->text('mission')->nullable()->after('vision'); // newline-separated
            $table->string('nss', 30)->nullable()->after('npsn');
            $table->string('postal_code', 10)->nullable()->after('address');
            $table->string('fax', 30)->nullable()->after('phone');
            $table->string('latitude')->nullable()->after('accreditation');
            $table->string('longitude')->nullable()->after('latitude');
            $table->string('elevation', 20)->nullable()->after('longitude');
            $table->string('study_hours', 30)->nullable()->after('elevation');
            $table->string('status', 30)->nullable()->after('education_level'); // Negeri/Swasta
        });
    }

    public function down(): void
    {
        Schema::table('school_profiles', function (Blueprint $table) {
            $table->dropColumn([
                'vision', 'mission', 'nss', 'postal_code', 'fax',
                'latitude', 'longitude', 'elevation', 'study_hours', 'status',
            ]);
        });
    }
};
