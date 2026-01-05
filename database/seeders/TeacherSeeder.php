<?php

namespace Database\Seeders;

use App\Models\TeacherProfile;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TeacherSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::updateOrCreate(
            ['username' => 'guru1'],
            [
                'name' => 'Guru Pertama',
                'email' => 'guru1@example.com',
                'password' => Hash::make('password123'),
                'user_type' => 'teacher',
                'active' => true,
            ]
        );

        TeacherProfile::updateOrCreate(
            ['user_id' => $user->id],
            [
                'nip' => 'NIP-0001',
                'subject' => 'Matematika',
            ]
        );
    }
}
