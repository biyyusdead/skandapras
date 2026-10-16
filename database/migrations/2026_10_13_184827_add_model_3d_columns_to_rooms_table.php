<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            $table->decimal('model_3d_x', 8, 2)->nullable()->after('map_y');
            $table->decimal('model_3d_y', 8, 2)->nullable()->after('model_3d_x');
            $table->decimal('model_3d_w', 8, 2)->nullable()->default(90)->after('model_3d_y');
            $table->decimal('model_3d_h', 8, 2)->nullable()->default(52)->after('model_3d_w');
            $table->decimal('model_3d_height', 5, 2)->nullable()->default(3)->after('model_3d_h');
            $table->string('model_3d_shape', 20)->nullable()->default('pitched')->after('model_3d_height');
            $table->string('model_3d_color', 10)->nullable()->default('#7fa8ee')->after('model_3d_shape');
            $table->boolean('model_3d_visible')->default(true)->after('model_3d_color');
        });
    }

    public function down(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            $table->dropColumn([
                'model_3d_x', 'model_3d_y', 'model_3d_w', 'model_3d_h',
                'model_3d_height', 'model_3d_shape', 'model_3d_color', 'model_3d_visible',
            ]);
        });
    }
};
