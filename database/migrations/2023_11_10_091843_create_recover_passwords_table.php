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
        Schema::create('recover_passwords', function (Blueprint $table) {
            $table->id();
            $table->string('token');
            $table->timestamp('expires_at');
            $table->timestamp('last_reset_at')->nullable();
            $table->timestamps();

            $table->index(['token']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recover_passwords');
    }
};
