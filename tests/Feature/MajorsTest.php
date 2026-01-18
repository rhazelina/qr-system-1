<?php

use App\Models\Major;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

it('allows admin to create a major', function () {
    $admin = User::create([
        'name' => 'Admin',
        'username' => 'admin',
        'email' => 'admin@example.com',
        'password' => Hash::make('secret123'),
        'user_type' => 'admin',
        'active' => true,
    ]);

    Sanctum::actingAs($admin);

    $response = $this->postJson('/api/majors', [
        'code' => 'RPL',
        'name' => 'Rekayasa Perangkat Lunak',
        'category' => 'Teknologi Informasi',
    ]);

    $response->assertStatus(201)
        ->assertJsonFragment(['code' => 'RPL']);

    expect(Major::count())->toBe(1);
});
