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
        Schema::table('damage_reports', function (Blueprint $table) {
            if (! Schema::hasColumn('damage_reports', 'reporter_type')) {
                $table->string('reporter_type', 30)->nullable()->default('pengunjung')->after('reporter_id');
            }
            if (! Schema::hasColumn('damage_reports', 'reporter_name')) {
                $table->string('reporter_name')->nullable()->after('reporter_type');
            }
            if (! Schema::hasColumn('damage_reports', 'reporter_phone')) {
                $table->string('reporter_phone', 30)->nullable()->after('reporter_name');
            }
            if (! Schema::hasColumn('damage_reports', 'reporter_identifier')) {
                $table->string('reporter_identifier', 50)->nullable()->after('reporter_phone');
            }
            if (! Schema::hasColumn('damage_reports', 'student_class')) {
                $table->string('student_class', 50)->nullable()->after('reporter_identifier');
            }
            if (! Schema::hasColumn('damage_reports', 'student_major')) {
                $table->string('student_major', 100)->nullable()->after('student_class');
            }

            if (! Schema::hasColumn('damage_reports', 'identity_status')) {
                $table->string('identity_status', 30)->nullable()->default('menunggu_verifikasi')->after('student_major');
            }
            if (! Schema::hasColumn('damage_reports', 'identity_note')) {
                $table->text('identity_note')->nullable()->after('identity_status');
            }

            if (! Schema::hasColumn('damage_reports', 'validity_status')) {
                $table->string('validity_status', 30)->nullable()->default('menunggu_pemeriksaan')->after('identity_note');
            }
            if (! Schema::hasColumn('damage_reports', 'rejection_reason')) {
                $table->text('rejection_reason')->nullable()->after('validity_status');
            }

            if (! Schema::hasColumn('damage_reports', 'damage_category')) {
                $table->string('damage_category', 20)->nullable()->after('rejection_reason');
            }
            if (! Schema::hasColumn('damage_reports', 'category_note')) {
                $table->text('category_note')->nullable()->after('damage_category');
            }
        });

        Schema::table('damage_reports', function (Blueprint $table) {
            if (! Schema::hasIndex('damage_reports', 'damage_reports_reporter_type_index')) {
                $table->index('reporter_type');
            }
            if (! Schema::hasIndex('damage_reports', 'damage_reports_identity_status_index')) {
                $table->index('identity_status');
            }
            if (! Schema::hasIndex('damage_reports', 'damage_reports_validity_status_index')) {
                $table->index('validity_status');
            }
            if (! Schema::hasIndex('damage_reports', 'damage_reports_damage_category_index')) {
                $table->index('damage_category');
            }
        });
    }

    public function down(): void
    {
        Schema::table('damage_reports', function (Blueprint $table) {
            if (Schema::hasIndex('damage_reports', 'damage_reports_reporter_type_index')) {
                $table->dropIndex(['reporter_type']);
            }
            if (Schema::hasIndex('damage_reports', 'damage_reports_identity_status_index')) {
                $table->dropIndex(['identity_status']);
            }
            if (Schema::hasIndex('damage_reports', 'damage_reports_validity_status_index')) {
                $table->dropIndex(['validity_status']);
            }
            if (Schema::hasIndex('damage_reports', 'damage_reports_damage_category_index')) {
                $table->dropIndex(['damage_category']);
            }

            $columnsToDrop = array_filter([
                'reporter_type',
                'reporter_name',
                'reporter_phone',
                'reporter_identifier',
                'student_class',
                'student_major',
                'identity_status',
                'identity_note',
                'validity_status',
                'rejection_reason',
                'damage_category',
                'category_note',
            ], fn (string $col): bool => Schema::hasColumn('damage_reports', $col));

            if (! empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }
};
