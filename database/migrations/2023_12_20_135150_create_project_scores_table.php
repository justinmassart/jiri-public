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
        Schema::create('project_scores', function (Blueprint $table) {
            $table->id();
            $table->float('score')->nullable();
            $table->text('comment')->nullable();
            $table->boolean('is_public')->default(false);
            $table->timestamps();

            $table->index(['score', 'is_public']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_scores');
    }
};
