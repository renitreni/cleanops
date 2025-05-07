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
        Schema::table('observations', function (Blueprint $table) {
            $table->dateTime('pending_at')->default(now());
            $table->dateTime('in_progress_at')->nullable();
            $table->dateTime('resolved_at')->nullable();
            $table->dateTime('rejected_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropColumn(['pending_at', 'in_progress_at', 'resolved_at', 'rejected_at']);
        });
    }
};
