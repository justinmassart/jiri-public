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
        Schema::table('jiris', function (Blueprint $table) {
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
        });

        Schema::table('contacts', function (Blueprint $table) {
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
        });

        Schema::table('attendances', function (Blueprint $table) {
            $table->foreignId('contact_id')->constrained()->cascadeOnDelete();
            $table->foreignId('jiri_id')->constrained()->cascadeOnDelete();
        });

        Schema::table('projects', function (Blueprint $table) {
            $table->foreignId('contact_id')->constrained()->cascadeOnDelete();
            $table->foreign('jiri_project_id')->references('id')->on('jiri_projects')->constrained()->cascadeOnDelete();
        });

        Schema::table('access_tokens', function (Blueprint $table) {
            $table->foreignId('contact_id')->constrained()->cascadeOnDelete();
            $table->foreignId('jiri_id')->constrained()->cascadeOnDelete();
        });

        Schema::table('contact_images', function (Blueprint $table) {
            $table->foreignId('contact_id')->constrained()->cascadeOnDelete();
        });

        Schema::table('recover_passwords', function (Blueprint $table) {
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
        });

        Schema::table('jiri_projects', function (Blueprint $table) {
            $table->foreignId('jiri_id')->constrained()->cascadeOnDelete();
        });

        Schema::table('presentations', function (Blueprint $table) {
            $table->foreignId('jiri_id')->constrained()->cascadeOnDelete();
            $table->foreignId('student_attendance_id')->constrained('attendances')->cascadeOnDelete();
            $table->foreignId('evaluator_attendance_id')->constrained('attendances')->cascadeOnDelete();
        });

        Schema::table('global_scores', function (Blueprint $table) {
            $table->foreignId('student_attendance_id')->constrained('attendances')->cascadeOnDelete();
            $table->foreignId('evaluator_attendance_id')->constrained('attendances')->cascadeOnDelete();
            $table->foreignId('jiri_id')->constrained()->cascadeOnDelete();
        });

        Schema::table('project_scores', function (Blueprint $table) {
            $table->foreignId('student_attendance_id')->constrained('attendances')->cascadeOnDelete();
            $table->foreignId('evaluator_attendance_id')->constrained('attendances')->cascadeOnDelete();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->foreignId('jiri_id')->constrained()->cascadeOnDelete();
        });

        Schema::table('ongoing_evaluations', function (Blueprint $table) {
            $table->foreignId('student_attendance_id')->constrained('attendances')->cascadeOnDelete();
            $table->foreignId('evaluator_attendance_id')->constrained('attendances')->cascadeOnDelete();
            $table->foreignId('jiri_id')->constrained()->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('jiris', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });

        Schema::table('contacts', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });

        Schema::table('attendances', function (Blueprint $table) {
            $table->dropForeign(['contact_id']);
            $table->dropForeign(['jiri_id']);
        });

        Schema::table('projects', function (Blueprint $table) {
            $table->dropForeign(['contact_id']);
            $table->dropForeign(['jiri_project_id']);
        });

        Schema::table('implementations', function (Blueprint $table) {
            $table->dropForeign(['project_id']);
            $table->dropForeign(['contact_id']);
            $table->dropForeign(['jiri_id']);
        });

        Schema::table('access_tokens', function (Blueprint $table) {
            $table->dropForeign(['contact_id']);
            $table->dropForeign(['jiri_id']);
        });

        Schema::table('contact_images', function (Blueprint $table) {
            $table->dropForeign(['contact_id']);
        });

        Schema::table('recover_passwords', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });

        Schema::table('jiri_projects', function (Blueprint $table) {
            $table->dropForeign(['jiri_id']);
        });

        Schema::table('presentations', function (Blueprint $table) {
            $table->dropForeign(['jiri_id']);
            $table->dropForeign(['student_attendance_id']);
            $table->dropForeign(['evaluator_attendance_id']);
        });

        Schema::table('global_scores', function (Blueprint $table) {
            $table->dropForeign(['student_attendance_id']);
            $table->dropForeign(['evaluator_attendance_id']);
            $table->dropForeign(['jiri_id']);
        });

        Schema::table('project_scores', function (Blueprint $table) {
            $table->dropForeign(['student_attendance_id']);
            $table->dropForeign(['evaluator_attendance_id']);
            $table->dropForeign(['project_id']);
            $table->dropForeign(['jiri_id']);
        });
    }
};
