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
        Schema::create('jiris', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamp('starts_at');
            $table->timestamp('ends_at');
            $table->string('slug');
            $table->enum('status', ['pending', 'started', 'ended'])->default('pending');
            $table->enum('session', ['january', 'june', 'september']);
            $table->timestamps();

            $table->index(['name', 'starts_at', 'ends_at', 'slug', 'session']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jiris');
    }
};
