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
        if (! Schema::hasTable('chatbot_usages')) {
            Schema::create('chatbot_usages', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->string('session_id', 64)->index();
                $table->string('ip_address', 45)->nullable();
                $table->unsignedInteger('prompt_tokens')->default(0);
                $table->unsignedInteger('completion_tokens')->default(0);
                $table->unsignedInteger('total_tokens')->default(0);
                $table->boolean('is_estimated')->default(false);
                $table->string('status', 30)->default('success')->index();
                $table->string('error_message', 255)->nullable();
                $table->unsignedInteger('response_time_ms')->nullable();
                $table->timestamps();

                $table->index(['user_id', 'created_at']);
                $table->index(['session_id', 'created_at']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chatbot_usages');
    }
};
