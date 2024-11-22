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
        Schema::create('global_scores', function (Blueprint $table) {
            $table->id();
            $table->float('global_score')->nullable();
            $table->text('global_comment')->nullable();
            $table->boolean('is_public')->default(false);
            $table->timestamps();

            $table->index(['global_score']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('global_scores');
    }
};
