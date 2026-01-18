<?php

use App\Models\AbsenceRequest;
use App\Models\Classes;
use App\Models\Schedule;
use App\Models\StudentProfile;
use App\Models\TeacherProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

it('allows teacher to submit and waka to approve absence request', function () {
    $class = Classes::create([
        'grade' => 'XI',
        'label' => 'TKJ 2',
    ]);

    $teacherUser = User::create([
        'name' => 'Guru',
        'username' => 'guru2',
        'email' => 'guru2@example.com',
        'password' => Hash::make('secret123'),
        'user_type' => 'teacher',
        'active' => true,
    ]);
    $teacherProfile = $teacherUser->teacherProfile()->create([
        'nip' => 'T-456',
        'homeroom_class_id' => $class->id,
    ]);

    Schedule::create([
        'day' => 'Monday',
        'start_time' => '07:00',
        'end_time' => '08:00',
        'title' => 'Bahasa Indonesia',
        'teacher_id' => $teacherProfile->id,
        'class_id' => $class->id,
        'semester' => 1,
        'year' => 2025,
    ]);

    $studentUser = User::create([
        'name' => 'Siswa',
        'username' => 'siswa1',
        'email' => 'siswa1@example.com',
        'password' => Hash::make('secret123'),
        'user_type' => 'student',
        'active' => true,
    ]);
    $studentProfile = StudentProfile::create([
        'user_id' => $studentUser->id,
        'nisn' => '99887766',
        'nis' => '123456',
        'gender' => 'L',
        'address' => 'Alamat',
        'class_id' => $class->id,
    ]);

    Sanctum::actingAs($teacherUser);

    $response = $this->postJson('/api/absence-requests', [
        'student_id' => $studentProfile->id,
        'type' => 'sick',
        'start_date' => '2025-01-10',
        'end_date' => '2025-01-12',
        'reason' => 'Surat dokter',
    ]);

    $response->assertStatus(201);

    $requestId = $response->json('id');
    expect($requestId)->not->toBeNull();

    $waka = User::create([
        'name' => 'Waka',
        'username' => 'waka2',
        'email' => 'waka2@example.com',
        'password' => Hash::make('secret123'),
        'user_type' => 'admin',
        'active' => true,
    ]);
    $waka->adminProfile()->create(['type' => 'waka']);

    Sanctum::actingAs($waka);

    $approve = $this->postJson('/api/absence-requests/'.$requestId.'/approve', [
        'approver_signature' => 'signed-by-waka',
    ]);

    $approve->assertStatus(200)
        ->assertJsonFragment(['status' => 'approved']);

    expect(AbsenceRequest::find($requestId)->status)->toBe('approved');
});
