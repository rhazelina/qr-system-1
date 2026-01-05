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
        Schema::table('student_profiles', function (Blueprint $table) {
            $table->foreignId('class_id')
                ->nullable()
                ->after('gender')
                ->constrained('classes')
                ->nullOnDelete();
        });

        Schema::table('teacher_profiles', function (Blueprint $table) {
            $table->foreignId('homeroom_class_id')
                ->nullable()
                ->after('nip')
                ->constrained('classes')
                ->nullOnDelete();

            $table->string('subject')->nullable()->after('homeroom_class_id');
        });

        Schema::table('qrcodes', function (Blueprint $table) {
            $table->enum('type', ['student', 'teacher'])->default('student')->after('token');
            $table->foreignId('schedule_id')->nullable()->after('type')->constrained('schedules')->nullOnDelete();
            $table->foreignId('issued_by')->nullable()->after('schedule_id')->constrained('users')->nullOnDelete();
            $table->dateTime('expires_at')->nullable()->after('status');
            $table->boolean('is_active')->default(true)->after('expires_at');
        });

        Schema::table('attendances', function (Blueprint $table) {
            $table->enum('attendee_type', ['student', 'teacher'])->after('id');
            $table->enum('status', ['present', 'late', 'excused', 'sick', 'absent'])->default('present')->after('reason_file');
            $table->dateTime('checked_in_at')->nullable()->after('status');
            $table->foreignId('schedule_id')->nullable()->after('qrcode_id')->constrained('schedules')->nullOnDelete();
            $table->string('source')->nullable()->after('schedule_id');

            $table->unique(['attendee_type', 'student_id', 'teacher_id', 'schedule_id'], 'attendance_unique_per_session');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropUnique('attendance_unique_per_session');
            $table->dropColumn(['attendee_type', 'status', 'checked_in_at', 'schedule_id', 'source']);
        });

        Schema::table('qrcodes', function (Blueprint $table) {
            $table->dropColumn(['type', 'schedule_id', 'issued_by', 'expires_at', 'is_active']);
        });

        Schema::table('teacher_profiles', function (Blueprint $table) {
            $table->dropConstrainedForeignId('homeroom_class_id');
            $table->dropColumn(['homeroom_class_id', 'subject']);
        });

        Schema::table('student_profiles', function (Blueprint $table) {
            $table->dropConstrainedForeignId('class_id');
            $table->dropColumn('class_id');
        });
    }
};
