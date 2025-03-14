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
        Schema::create('observations', function (Blueprint $table) {
            $table->id();
            $table->text('serial');
            $table->text('name')->nullable();
            $table->text('description');
            $table->text('email')->nullable();
            $table->text('contact_no')->nullable();
            $table->longText('photo')->nullable();
            $table->json('location'); // Store lat, lng as JSON
            $table->string('reported_by')->nullable();
            $table->enum('status', ['pending', 'in_progress', 'resolved', 'rejected'])->default('pending');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('observations');
    }
};
