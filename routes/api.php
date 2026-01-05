<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\ClassController;
use App\Http\Controllers\DeviceController;
use App\Http\Controllers\QrCodeController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\SchoolYearController;
use App\Http\Controllers\SemesterController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\TimeSlotController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\TeacherController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/auth/login', [AuthController::class, 'login'])->middleware('throttle:login');

Route::middleware('auth:sanctum')->group(function (): void {
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/auth/logout', [AuthController::class, 'logout']);

    Route::middleware('role:admin')->group(function (): void {
        Route::apiResource('classes', ClassController::class);
        Route::apiResource('teachers', TeacherController::class);
        Route::post('/teachers/import', [TeacherController::class, 'import']);
        Route::apiResource('students', StudentController::class);
        Route::post('/students/import', [StudentController::class, 'import']);
        Route::apiResource('schedules', ScheduleController::class)->except(['index', 'show']);
        Route::apiResource('school-years', SchoolYearController::class);
        Route::apiResource('semesters', SemesterController::class);
        Route::apiResource('rooms', RoomController::class);
        Route::apiResource('subjects', SubjectController::class);
        Route::apiResource('time-slots', TimeSlotController::class);
    });

    Route::middleware('role:admin,teacher')->group(function (): void {
        Route::get('/schedules', [ScheduleController::class, 'index']);
        Route::get('/schedules/{schedule}', [ScheduleController::class, 'show']);
        Route::get('/qrcodes/active', [QrCodeController::class, 'active']);
        Route::post('/qrcodes/generate', [QrCodeController::class, 'generate']);
        Route::post('/qrcodes/{token}/revoke', [QrCodeController::class, 'revoke']);
    });

    Route::middleware('role:admin,teacher,student')->group(function (): void {
        Route::post('/attendance/scan', [AttendanceController::class, 'scan'])->middleware('throttle:scan');
    });

    Route::middleware('role:admin,teacher')->group(function (): void {
        Route::get('/attendance/schedules/{schedule}', [AttendanceController::class, 'bySchedule']);
        Route::post('/attendance/{attendance}/excuse', [AttendanceController::class, 'markExcuse']);
        Route::get('/attendance/export', [AttendanceController::class, 'export']);
        Route::get('/attendance/recap', [AttendanceController::class, 'recap']);
        Route::get('/attendance/schedules/{schedule}/summary', [AttendanceController::class, 'summaryBySchedule']);
        Route::get('/attendance/classes/{class}/summary', [AttendanceController::class, 'summaryByClass']);
        Route::post('/attendance/{attendance}/attachments', [AttendanceController::class, 'attach']);
        Route::post('/attendance/{attendance}/void', [AttendanceController::class, 'void']);
    });

    Route::middleware('role:student')->group(function (): void {
        Route::get('/me/attendance', [AttendanceController::class, 'me']);
        Route::post('/me/devices', [DeviceController::class, 'store']);
        Route::delete('/me/devices/{device}', [DeviceController::class, 'destroy']);
    });
});
