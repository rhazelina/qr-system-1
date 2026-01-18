<?php

use App\Models\Classes;
use App\Models\Schedule;
use App\Models\TeacherProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

it('allows waka to bulk replace schedules per day', function () {
    $waka = User::create([
        'name' => 'Waka',
        'username' => 'waka',
        'email' => 'waka@example.com',
        'password' => Hash::make('secret123'),
        'user_type' => 'admin',
        'active' => true,
    ]);
    $waka->adminProfile()->create(['type' => 'waka']);

    $class = Classes::create([
        'grade' => 'X',
        'label' => 'RPL 1',
    ]);

    $teacherUser = User::create([
        'name' => 'Guru',
        'username' => 'guru1',
        'email' => 'guru1@example.com',
        'password' => Hash::make('secret123'),
        'user_type' => 'teacher',
        'active' => true,
    ]);
    $teacherProfile = TeacherProfile::create([
        'user_id' => $teacherUser->id,
        'nip' => 'T-123',
    ]);

    Sanctum::actingAs($waka);

    $response = $this->postJson('/api/classes/'.$class->id.'/schedules/bulk', [
        'day' => 'Senin',
        'semester' => 1,
        'year' => 2025,
        'items' => [
            [
                'subject_name' => 'Matematika',
                'teacher_id' => $teacherProfile->id,
                'start_time' => '07:00',
                'end_time' => '08:00',
            ],
            [
                'subject_name' => 'Fisika',
                'teacher_id' => $teacherProfile->id,
                'start_time' => '08:10',
                'end_time' => '09:00',
            ],
        ],
    ]);

    $response->assertStatus(200)
        ->assertJsonFragment(['count' => 2]);

    expect(Schedule::where('class_id', $class->id)->count())->toBe(2);
    expect(Schedule::first()->day)->toBe('Monday');
});
